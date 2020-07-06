<?php
/**
 * @file
 * Contains Drupal\form\Form\SettingsForm.
 */

namespace Drupal\form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Utility\EmailValidator;


/**
 * Class SettingsForm.
 *
 * @package Drupal\form\Form
 */

class ModuleConfigurationForm extends ConfigFormBase {
 
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'myform.settings',
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }
public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('myform.settings');
    $form['body'] = array (
      '#type' => 'textarea',
      '#title' => $this->t('Email Text'),
      '#default_value' => $config->get('body'),
    );
    return parent::buildForm($form, $form_state);
}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('myform.settings')
      ->set('body', $form_state->getValue('body'))
      ->save();
   }
}
