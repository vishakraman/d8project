<?php

namespace Drupal\form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Utility\EmailValidator;

/**
 * Our HelloForm class extends Formbase.
 */
class HelloForm extends FormBase {

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The email validator.
   *
   * @var \Drupal\Component\Utility\EmailValidator
   */
  protected $emailValidator;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new EmailExampleGetFormPage.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Component\Utility\EmailValidator $email_validator
   *   The email validator.
   */
  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, EmailValidator $email_validator) {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->emailValidator = $email_validator;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $form = new static(
      $container->get('plugin.manager.mail'),
      $container->get('language_manager'),
      $container->get('email.validator')
    );
    $form->setMessenger($container->get('messenger'));
    $form->setStringTranslation($container->get('string_translation'));
    return $form;
  }

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
    $form_values = $form_state->getValues();

    // All system mails need to specify the module and template key (mirrored
    // from hook_mail()) that the message they want to send comes from.
    $module = 'form';
    $key = 'custom_mail';

    // Specify 'to' and 'from' addresses.
    $to = $this->config('system.site')->get('mail');
    $from = $this->config('system.site')->get('mail');

    // "params" loads in additional context for email content completion in
    // hook_mail(). In this case, we want to pass in the values the user entered
    // into the form, which include the message body in $form_values['message'].
    $params = $form_values;
    $language_code = $this->languageManager->getDefaultLanguage()->getId();

    // Whether or not to automatically send the mail when we call mail() on the
    // mail manager. This defaults to TRUE, and is normally what you want unless
    // you need to do additional processing before the mail manager sends the
    // message.
    $send_now = TRUE;
    // Send the mail, and check for success. Note that this does not guarantee
    // message delivery; only that there were no PHP-related issues encountered
    // while sending.
    $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);
    $result2 = $this->mailManager->mail($module, 'ak_mail', $to, $language_code, $params, $from, $send_now);
    if ($result['result'] == TRUE) {
      $this->messenger()->addMessage($this->t('Your message has been sent.'));
    }
    else {
      $this->messenger()->addMessage($this->t('There was a problem sending your message and it was not sent.'), 'error');
    }
    if ($result2['result'] == TRUE) {
      $this->messenger()->addMessage($this->t('Your message has been sent.'));
    }
    else {
      $this->messenger()->addMessage($this->t('There was a problem sending your message and it was not sent.'), 'error');
    }
    //DB INSERT
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
    $url = Url::fromRoute('hello.getdetails');
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
