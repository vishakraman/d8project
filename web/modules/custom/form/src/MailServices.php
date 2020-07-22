<?php

/**
* @file providing the service that say hello world and hello 'given name'.
*
*/

namespace  Drupal\form;

use Drupal\Core\Mail\MailManagerInterface;

class MailServices {

 public function sayHello($module, $key, $to, array $params, $langcode, $form, $send){
	 $mailManager = \Drupal::service('plugin.manager.mail');

 $result = $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);
 if ($result['result'] !== TRUE) {
   drupal_set_message(t('There was a problem sending your message and it was nost sent.'), 'error');
 }
 else {
   drupal_set_message(t('Your message has been sent.'));
 }

}
}