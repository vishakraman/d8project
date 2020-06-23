<?php
/**
 * @file
 * Contains \Drupal\form\Form\HelloForm
 */

namespace Drupal\form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class HelloForm extends FormBase
{
  /*
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'hello_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name'),
//      '#pattern' => '[A-Za-z]+',
      '#required' => TRUE
    );

    $form['email_address'] = array(
      '#type' => 'email',
      '#title' => $this->t('Email:'),
      '#pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
      '#required' => TRUE
    );
    $form['pincode'] = array(
      '#type' => 'textfield',
      '#title' => t('Pincode'),
      '#size' => 6,
      '#pattern' => '[0-9]{6}',
      '#required' => TRUE
    );
    $form['employee_role'] = array(
      '#type' => 'select',
      '#title' => ('Employee Role'),
      '#options' => array(
        'manager' => t('Manager'),
        'executive_manager' => t('Executive Manager'),
        'fresher' => t('Fresher'),
        'ceo' => t('CEO'),
        'team_lead' => t('Team Lead'),
      ),
    );
    $form['expiration'] = [
      '#type' => 'date',
      '#title' => $this
        ->t('Date'),
      '#default_value' => '2020-02-05',
    ];
    $form['phone'] = array(
      '#type' => 'tel',
      '#title' => $this
        ->t('Phone'),
//      '#pattern' => '[^\\d]*',
    );
//    $form['tests_taken'] = [
//      '#type' => 'checkboxes',
//      '#options' => ['SAT' => $this->t('SAT'), 'ACT' => $this->t('ACT')],
//      '#title' => $this->t('What standardized tests did you take?'),
//      '#description' => 'Checkboxes, #type = checkboxes',
//    ];

    $form['settings']['active'] = [
      '#type' => 'radios',
      '#title' => $this->t('Poll status'),
      '#options' => [0 => $this->t('Closed'), 1 => $this->t('Active')],
      '#description' => $this->t('Radios, #type = radios'),
    ];

    $form['textarea'] = array(
      '#type' => 'textarea',
      '#title' => $this
        ->t('Text'),
    );
    $form['copy'] = array(
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Terms & Conditions'),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;
  }


  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    //code to send mail
//    $mailManager = \Drupal::service('plugin.manager.mail');
//    $langcode = \Drupal::currentUser()->getPreferredLangcode();
//    $params['context']['subject'] = $form_state->getValue('first_name');
//    $params['context']['message'] = 'Yaay';
//    $to = $form_state->getValue('email_address');
//    $mailManager->mail('system', 'mail', $to, $langcode, $params);


    $conn = Database::getConnection();
    $conn->insert('employee')->fields(
      array(
        'first_name' => $form_state->getValue('first_name'),
        'email' => $form_state->getValue('email_address'),
        'pincode' => $form_state->getValue('pincode'),
        'role' => $form_state->getValue('employee_role'),
        'expiration' => $form_state->getValue('expiration'),
        'phone' => $form_state->getValue('phone'),
        'active' => $form_state->getValue('active'),
        'textarea' => $form_state->getValue('textarea'),
        'copy' => $form_state->getValue('copy'),
      )
    )->execute();
    $url = Url::fromRoute('hello.thankyou');
    $form_state->setRedirectUrl($url);

  }

  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    //email validation
    if (!filter_var($form_state->getValue('email_address', FILTER_VALIDATE_EMAIL))) {
      $form_state->setErrorByName('email_address', $this->t('The Email Address you have provided is invalid.'));
    }
    //phone number validation
    if (strlen($form_state->getValue('phone')) < 3) {
      $form_state->setErrorByName('phone', $this->t('The phone number is too short. Please enter a full phone number.'));
    }
    //first name validation
    if (strlen($form_state->getValue('first_name')) < 5) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('The title must be at least 10 characters long.'));
    }
    //copy terms
    if (empty($form_state->getValue('copy'))) {
      // Set an error for the form element with a key of "accept".
      $form_state->setErrorByName('accept', $this->t('You must accept the terms of use to continue'));
    }
  }
}
