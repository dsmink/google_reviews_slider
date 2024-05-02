<?php

namespace Drupal\google_reviews\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Module settings form.
 */
class ReviewSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'review_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $config = $this->config('google_reviews.settings');

    // API key field.
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $config->get('google_reviews.api_key'),
      '#required' => TRUE,
    ];
    // Place id field.
    $form['place_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Place IDs (Separate by a comma and space if adding more than one)'),
      '#default_value' => $config->get('google_reviews.place_id'),
      '#required' => TRUE,
    ];
    // Minimum rating field.
    $form['minimum_rating'] = [
      '#type' => 'select',
      '#title' => $this->t('Minimum rating'),
      '#default_value' => $config->get('google_reviews.minimum_rating') ?? 0,
      '#required' => TRUE,
      '#options' => [
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
      ],
    ];
    // Maximum displayed review messages.
    $form['max_messages_displayed'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum displayed review messages'),
      '#default_value' => $config->get('google_reviews.max_messages_displayed'),
      '#required' => FALSE,
    ];
    // Maximum review lifespan.
    $form['max_time_displayed'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum review time'),
      '#default_value' => $config->get('google_reviews.max_time_displayed'),
      '#placeholder' => $this->t('In years'),
      '#required' => FALSE,
    ];
    // Title above the block.
    $form['review_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title above the block'),
      '#default_value' => $config->get('google_reviews.review_title'),
      '#required' => FALSE,
    ];
    // Bool to display the global rating.
    $form['display_rating'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display the global rating'),
      '#default_value' => $config->get('google_reviews.display_rating'),
      '#required' => FALSE,
    ];
    // Link to the page to add reviews.
    $form['review_page_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link to the page to add reviews'),
      '#default_value' => $config->get('google_reviews.review_page_link'),
      '#placeholder' => $this->t('https://'),
      '#required' => FALSE,
    ];
    // Text of the review page link.
    $form['review_page_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text of the review page link'),
      '#default_value' => $config->get('google_reviews.review_page_message'),
      '#required' => FALSE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Fetch reviews'),
      '#submit' => ['::submitForm', '::fetchReviews'],
      '#element_validate' => ['::validateForm'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('google_reviews.settings');
    $config->set('google_reviews.api_key', $form_state->getValue('api_key'));
    $config->set('google_reviews.place_id', $form_state->getValue('place_id'));
    $config->set('google_reviews.minimum_rating', $form_state->getValue('minimum_rating'));
    $config->set('google_reviews.max_messages_displayed', $form_state->getValue('max_messages_displayed'));
    $config->set('google_reviews.review_page_link', $form_state->getValue('review_page_link'));
    $config->set('google_reviews.review_page_message', $form_state->getValue('review_page_message'));
    $config->set('google_reviews.review_title', $form_state->getValue('review_title'));
    $config->set('google_reviews.display_rating', $form_state->getValue('display_rating'));
    $config->set('google_reviews.max_time_displayed', $form_state->getValue('max_time_displayed'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('api_key') == NULL) {
      $form_state->setErrorByName('api_key', $this->t('Please enter a valid API key.'));
    }
    if ($form_state->getValue('place_id') == NULL) {
      $form_state->setErrorByName('place_id', $this->t('Please enter a valid place id.'));
    }
    if (
      $form_state->getValue('minimum_rating') == NULL
      || !is_numeric($form_state->getValue('minimum_rating'))
      || $form_state->getValue('minimum_rating') > 5
      || $form_state->getValue('minimum_rating') < 0
    ) {
      $form_state->setErrorByName('minimum_rating', $this->t('Please enter a valid minimum rating from 0 to 5.'));
    }
  }

  /**
   * Redirects to the reviews page where reviews will be fetched.
   */
  public function fetchReviews(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('google_reviews.add_reviews');
    return;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'google_reviews.settings',
    ];
  }

}
