<?php

namespace Drupal\form\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for form module.
 */
class ThankyouController extends ControllerBase {

  /**
   * Returns a simple page.
   */
  public function successPage() {
    // Display thank you page.
    $element = [
      '#markup' => 'Form data submitted',
    ];
    return $element;
  }

  /**
   * Fetch from the db and return.
   */
  public function getDetails() {
    // Fetch data from the employee table.
    $db = \Drupal::database();
    $query = $db->select('employee', 'n');
    $query->fields('n');
    $response = $query->execute()->fetchAll();
    $rows = [];
    foreach ($response as $row => $content) {
      $rows[] = [
        'data' => [$content->pid, $content->first_name, $content->email, $content->pincode,
          $content->role, $content->expiration, $content->phone, $content->active, $content->textarea, $content->copy,
        ],
      ];
    }

    // Create the header.
    $header = ['pid', 'first_name', 'email_address', 'pincode', 'employee_role',
      'expiration', 'phone', 'active', 'textarea', 'copy',
    ];
    $output = [
    // Here you can write #type also instead of #theme.
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
    return $output;
  }

}
