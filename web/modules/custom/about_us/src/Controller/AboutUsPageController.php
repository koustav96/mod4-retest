<?php

namespace Drupal\about_us\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

class AboutUsPageController extends ControllerBase {

  public function content() {
    // Retrieve the submitted leadership data from state.
    $name = \Drupal::state()->get('submitted_name');
    $designation = \Drupal::state()->get('submitted_designation');
    $linkedin = \Drupal::state()->get('submitted_linkedin');
    $profile_image_id = \Drupal::state()->get('submitted_profile_image');

    $profile_image_url = '';
    if ($profile_image_id) {
      $file = File::load($profile_image_id);
      if ($file) {
        $profile_image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
      }
    }

    // Retrieve the Best Anchor of the Week from state.
    $best_anchor_id = \Drupal::state()->get('best_anchor_of_the_week');
    $best_anchor_name = 'None';
    if ($best_anchor_id) {
      $best_anchor = Node::load($best_anchor_id);
      $best_anchor_name = $best_anchor ? $best_anchor->getTitle() : 'Unknown';
    }

    // Build the content markup.
    $content = '<h3>Leadership!</h3>';
    $content .= '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>';
    $content .= '<p><strong>Designation:</strong> ' . htmlspecialchars($designation) . '</p>';
    $content .= '<p><strong>LinkedIn Profile Link:</strong> <a href="' . htmlspecialchars($linkedin) . '">' . htmlspecialchars($linkedin) . '</a></p>';
    if ($profile_image_url) {
      $content .= '<p><strong>Profile Image:</strong></p>';
      $content .= '<img src="' . htmlspecialchars($profile_image_url) . '" alt="' . htmlspecialchars($name) . '">';
    }

    // Add Best Anchor section.
    $content .= '<h3>Best Anchor of the Week</h3>';
    $content .= '<p><strong>Name:</strong> ' . htmlspecialchars($best_anchor_name) . '</p>';

    return [
      '#type' => 'markup',
      '#markup' => $content,
    ];
  }
}
