<?php

namespace Drupal\google_reviews\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ReviewsBlock' Block.
 *
 * @Block(
 *   id = "reviews_block",
 *   admin_label = @Translation("Google reviews content block"),
 *   category = @Translation("Reviews"),
 *   provider = "google_reviews",
 * )
 */
class ReviewsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the settings form the configuration form.
    $config = \Drupal::config('google_reviews.settings');
    $review_title = $config->get('google_reviews.review_title') ?? '';
    $display_rating = $config->get('google_reviews.display_rating') ?? FALSE;
    $review_link_url = $config->get('google_reviews.review_page_link') ?? '';
    $reviews_link_text = $config->get('google_reviews.review_page_message') ?? '';
    $minimum_rating = $config->get('google_reviews.minimum_rating') ?? 0;
    $max_messages_displayed = $config->get('google_reviews.max_messages_displayed');
    $max_time_displayed = $config->get('google_reviews.max_time_displayed');

    // Import the reviews from the content list of its type or else return a message for no reviews.
    $reviews = $this->getReviewsContent();
    if (empty($reviews)) {
      return [
        '#title' => $this->t('Reviews'),
        '#markup' => $this->t('No reviews to display'),
      ];
    }

    $total_rating = 0;
    $message = [
      'author_name' => '',
      'author_photo_url' => '',
      'author_rating' => '',
      'time' => '',
      'message' => '',
    ];
    $messages = [];

    foreach ($reviews as $review) {
      $rating = $review->get('field_review_rating')->value;
      $time = $review->get('field_review_time')->value;

      // Ignore reviews that are too old.
      if ($max_time_displayed && $time > $max_time_displayed) {
        continue;
      }

      // Add the rating to the total rating regardless of minimum_rating.
      $total_rating += $rating;
      // Only display reviews with a rating higher than minimum_rating.
      if ($rating >= $minimum_rating) {
        $message['author_name'] = $review->get('field_review_name')->value;
        $message['author_photo_url'] = $review->get('field_review_photo_url')->value;
        $message['author_rating'] = $rating;
        $message['time'] = $review->get('field_review_time_description')->value;
        $message['message'] = $review->get('field_review_message')->value;
        $messages[] = $message;
      }
    }

    // If display_rating is enabled then make a global rating.
    $global_rating = $display_rating ? round($total_rating / count($reviews), 1) : '';

    return [
      '#theme' => 'reviews_theme',
      '#rating' => $global_rating,
      '#messages' => $max_messages_displayed ? array_slice($messages, 0, $max_messages_displayed) : $messages,
      '#review_link_url' => $review_link_url,
      '#reviews_link_text' => $this->t($reviews_link_text),
      '#title' => $review_title,
    ];
  }

  /**
   * Method to get the published reviews from the list of active content of type 'reviews'.
   */
  private function getReviewsContent() {
    $entity_type_manager = \Drupal::entityTypeManager();
    $entity_storage_block = $entity_type_manager->getStorage('node');
    $reviews = $entity_storage_block->loadByProperties(
      [
        'type' => 'review',
        'status' => 1,
      ]
    );
    return $reviews;
  }

}
