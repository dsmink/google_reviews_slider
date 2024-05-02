<?php

namespace Drupal\google_reviews\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

/**
 * Review Controller.
 */
class ReviewController extends ControllerBase implements ContainerInjectionInterface {
  /**
   * Imported Review Service.
   *
   * @var \Drupal\google_reviews\ReviewService
   */
  private $reviewService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('google_reviews.reviews_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($review_service) {
    $this->reviewService = $review_service;
  }

  /**
   * Get reviews from the service and return an empty page.
   */
  public function fetchReviews() {
    $added_reviews = $this->reviewService->getReviews();
    return [
      '#title' => $this->t('Reviews'),
      '#markup' => $this->t($added_reviews . ' reviews added. Please validate in the content.'),
    ];
  }

}
