<?php

namespace Drupal\form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;

/**
 * Our HelloForm class extends Formbase.
 */
class HelloForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hello_form';
  }

  /**
   * Code to build the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => t('First Name'),
    // '#pattern' => '[A-Za-z]+',
      '#required' => TRUE,
    ];

    $form['email_address'] = [
      '#type' => 'email',
      '#title' => $this->t('Email:'),
      '#pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
      '#required' => TRUE,
    ];
    $form['pincode'] = [
      '#type' => 'textfield',
      '#title' => t('Pincode'),
      '#size' => 6,
      '#required' => TRUE,
    ];
    $form['employee_role'] = [
      '#type' => 'select',
      '#title' => ('Employee Role'),
      '#required' => TRUE,
      '#options' => [
        'manager' => t('Manager'),
        'executive_manager' => t('Executive Manager'),
        'fresher' => t('Fresher'),
        'ceo' => t('CEO'),
        'team_lead' => t('Team Lead'),
      ],
    ];
    $form['expiration'] = [
      '#type' => 'date',
      '#title' => $this
        ->t('Date'),
      '#default_value' => '2020-02-05',
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this
        ->t('Phone'),
    ];
    $form['settings']['active'] = [
      '#type' => 'radios',
      '#title' => $this->t('Poll status'),
      '#options' => [0 => $this->t('Closed'), 1 => $this->t('Active')],
    ];

    $form['textarea'] = [
      '#type' => 'textarea',
      '#title' => $this
        ->t('Text'),
    ];
    $form['copy'] = [
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Terms & Conditions'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * Submit form code.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $conn = Database::getConnection();
    $conn->insert('employee')->fields(
      [
        'first_name' => $form_state->getValue('first_name'),
        'email' => $form_state->getValue('email_address'),
        'pincode' => $form_state->getValue('pincode'),
        'role' => $form_state->getValue('employee_role'),
        'expiration' => $form_state->getValue('expiration'),
        'phone' => $form_state->getValue('phone'),
        'active' => $form_state->getValue('active'),
        'textarea' => $form_state->getValue('textarea'),
        'copy' => $form_state->getValue('copy'),
      ]
    )->execute();
    $url = Url::fromRoute('hello.thankyou');
    $form_state->setRedirectUrl($url);

  }

  /**
   * Code to validate the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Email validation.
    if (!filter_var($form_state->getValue('email_address', FILTER_VALIDATE_EMAIL))) {
      $form_state->setErrorByName('email_address', $this->t('The Email Address you have provided is invalid.'));
    }
    // Phone number validation.
    if (!preg_match("/^[+]?[1-9][0-9]{9,14}$/", $form_state->getValue('phone'))) {
      $form_state->setErrorByName('phone', $this->t('Please enter a valid phone number.'));
    }
    // First name validation.
    if (strlen($form_state->getValue('first_name')) < 5) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('The title must be at least 10 characters long.'));
    }
    // Copy terms.
    if (empty($form_state->getValue('copy'))) {
      // Set an error for the form element with a key of "accept".
      $form_state->setErrorByName('accept', $this->t('You must accept the terms of use to continue'));
    }
    // Pincode Validation
    if (strlen($form_state->getValue('pincode')) < 6) {
      $form_state->setErrorByName('pincode', $this->t('Pincode must be atleast 6 chars.'));
    }
  }

}
