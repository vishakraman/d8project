<?php
/**
 * @file
 * Contains \Drupal\form\Plugin\Block\CustomBlock.
 */

namespace Drupal\form\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Custom' block.
 *
 * @Block(
 *   id = "custom_block",
 *   admin_label = @Translation("Custom Form Block"),
 *   category = @Translation("Custom block form example")
 * )
 */

class CustomBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\form\Form\HelloForm');
    return $form;
  }
}
