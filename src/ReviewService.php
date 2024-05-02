<?php

namespace Drupal\google_reviews;

use Drupal\Component\Serialization\Json;
use Drupal\node\Entity\Node;
use Drupal\Core\Config\ConfigFactory;
use RuntimeException;

/**
 * Review Service.
 */
class ReviewService {
  const API_URL = "https://maps.googleapis.com/maps/api/place/details/json";

  /**
   * Configfactory from service parameter.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   * Configfactory service
   */
  protected $configFactory;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory->get('google_reviews.settings');
  }

  /**
   * Get the reviews from the Google places api and import them as a 'review' content and return the number of reviews added.
   */
  public function getReviews() {
    $client = \Drupal::httpClient();
    $api_key = $this->configFactory->get('google_reviews.api_key');
    $place_ids = $this->configFactory->get('google_reviews.place_id');
    $place_id_list = array_filter(explode(', ', $place_ids));

    if (count($place_id_list) <= 0 || !$api_key) {
      return 0;
    }

    $created_reviews = 0;
    foreach ($place_id_list as $place_id) {
      $result = $this->importReview($client, $place_id, $api_key);
      if ($result && !empty($result['result']['reviews'])) {
        foreach ($result['result']['reviews'] as $review) {
          if (!$this->isCreated($review)) {
            $this->createReview($review);
            $created_reviews++;
          }
        }
      }
    }
    return $created_reviews;
  }

  /**
   * Get the list of reviews from the Google Place API.
   */
  public function importReview($client, $place_id, $api_key) {
    $request = NULL;

    try {
      $request = $client->get(
        self::API_URL,
        [
          'query' => [
            'fields' => 'reviews',
            'place_id' => $place_id,
            'key' => $api_key,
          ],
        ]
      );
    }
    catch (RuntimeException $e) {
      \Drupal::logger('google_review_module')->error($e->getMessage());
    }

    if ($request && $request->getStatusCode() == 200) {
      $response = $request->getBody()->getContents();
      $result = json::decode($response);
      return $result;
    }
    return NULL;
  }

  /**
   * Check if the reviews is already imported as a content.
   */
  private function isCreated($review) {
    $entity_type_manager = \Drupal::entityTypeManager();
    $entity_storage_block = $entity_type_manager->getStorage('node');
    $reviews = $entity_storage_block->loadByProperties(
      [
        'type' => 'review',
        'title' => $this->getTitleOf($review),
      ]
    );

    return !empty($reviews);
  }

  /**
   * Create the review content.
   */
  private function createReview($review) {
    $node = Node::create(
      [
        'type' => 'review',
        'title' => $this->getTitleOf($review),
        'field_review_name' => $review['author_name'],
        'field_review_photo_url' => $review['profile_photo_url'],
        'field_review_language' => $review['language'],
        'field_review_rating' => $review['rating'],
        'field_review_time_description' => $review['relative_time_description'],
        'field_review_time' => $this->getTimeInYears($review),
        'field_review_message' => $review['text'],
        'status' => FALSE,
      ]
    );
    $node->save();
  }

  /**
   * Create the title of a review.
   */
  private function getTitleOf($review) {
    return $review['author_name'] . " : " . $review['rating'];
  }

  /**
   * Get the review duration in years based on the relative time.
   */
  private function getTimeInYears($review) {
    $time_description = $review['relative_time_description'];
    $time_in_years = 0;

    if ($time_description == "a year ago") {
      $time_description = "1 years ago";
    }

    if (strpos($time_description, 'years')) {
      $time_in_years = str_replace(' years ago', '', $time_description);
    }

    return $time_in_years;
  }

}
