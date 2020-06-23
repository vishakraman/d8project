<?php
namespace Drupal\form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;

/**
 * Provides route responses for form module
 */

class ThankyouController extends ControllerBase {
  /**
   * Returns a simple page
   */
  public function successPage() {
    //display thank you page
    $element = array(
      '#markup' =>'Form data submitted',
    );
    return $element;
  }

  public function getDetails() {
    //fetch data from the employee table
    $db = \Drupal::database();
    $query = $db->select('employee', 'n');
    $query->fields('n');
    $response = $query->execute()->fetchAll();
    $rows = array();
    foreach ($response as $row => $content) {
      $rows[] = array(
        'data' => array($content->pid, $content->first_name, $content->email, $content->pincode,
          $content->role, $content->expiration, $content->phone, $content->active, $content->textarea, $content->copy));
    }

    // Create the header.
    $header = array('pid', 'first_name', 'email_address', 'pincode','employee_role',
      'expiration', 'phone', 'active', 'textarea', 'copy');
    $output = array(
      '#theme' => 'table',    // Here you can write #type also instead of #theme.
      '#header' => $header,
      '#rows' => $rows,
      '#cache' => [
        'max-age' => 0,
      ],
    );
    return $output;
  }
}
