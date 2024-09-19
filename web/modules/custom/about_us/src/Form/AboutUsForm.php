<?php

namespace Drupal\about_us\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

/**
 * Provides a form for adding leadership details and Best Anchor of the Week.
 */
class AboutUsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'about_us_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['designation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Designation'),
      '#required' => TRUE,
    ];

    $form['linkedin'] = [
      '#type' => 'url',
      '#title' => $this->t('LinkedIn Profile Link'),
      '#required' => TRUE,
    ];

    $form['profile_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Profile Image'),
      '#upload_location' => 'public://profile_images/',
      '#default_value' => [],
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg gif'],
      ],
    ];

    // Best Anchor of the Week
    $form['best_anchor'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Best Anchor of the Week'),
      '#target_type' => 'node',
      '#selection_handler' => 'default',
      '#description' => $this->t('Select a news anchor node.'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
  
    // Process the form values for Leadership Details.
    $name = $values['name'];
    $designation = $values['designation'];
    $linkedin = $values['linkedin'];
    $profile_image = $values['profile_image'][0]; 
  
    // Load the file entity for profile image.
    $file = File::load($profile_image);
    if ($file) {
      $file->setPermanent();
      $file->save();
    }

    \Drupal::state()->set('submitted_name', $name);
    \Drupal::state()->set('submitted_designation', $designation);
    \Drupal::state()->set('submitted_linkedin', $linkedin);
    \Drupal::state()->set('submitted_profile_image', $profile_image);

    // Process the form values for Best Anchor of the Week.
    $best_anchor_id = $values['best_anchor'];
    \Drupal::state()->set('best_anchor_of_the_week', $best_anchor_id);
    // Redirect to the thank you page.
    $form_state->setRedirect('about_us.page');
  }
}
