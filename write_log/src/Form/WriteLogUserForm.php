<?php

namespace Drupal\write_log\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Email;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WriteLogUserForm.
 */
class WriteLogUserForm extends FormBase {


  /**
   * Form id
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'write_log_form';
  }

  /**
   * Build form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $current_user = \Drupal::currentUser();
    $config = $this->config('write_log.admin');

    if($current_user->isAnonymous()){
      $form['name'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Name'),
        '#required' => TRUE,
      );

      if($config->get('anonimous_can_send_contacts')){
        $form['phone'] = array(
          '#type' => 'tel',
          '#title' => $this->t('Phone'),
          '#description' => $this->t('The phone must be in the format +7(xxx)xxx-xx-xx')
        );

        $form['email'] = array(
          '#type' => 'email',
          '#title' => $this->t('Email'),
        );
      }
    }

    $form['text'] = array(
      '#type' => 'textarea',
      '#title' => $this
        ->t('Text'),
      '#required' => TRUE
    );

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Validate
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validate email
    $email = $form_state->getValue('email');

    /* @var \Egulias\EmailValidator\EmailValidator $email_validator */
    $email_validator = \Drupal::service('email.validator');
    if($email !== '' && !$email_validator->isValid($email)){
      $form_state->setErrorByName('email', $this->t('Email invalid'));
    }

    // Validate phone
    $phone = $form_state->getValue('phone');
    if($phone !== '' && !preg_match("/^(\+7)\([0-9]{3}\)[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $phone)) {
      $form_state->setErrorByName('phone', $this->t('Phone invalid'));
    }
  }

  /**
   * Submit
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $clean_values = $form_state->cleanValues()->getValues();

    \Drupal::logger('write_log')->notice(
      'Form submitted! Values: @values',
      [
        '@values' => http_build_query($clean_values)
      ]
    );

    drupal_set_message($this->t('Thank you for submission'));
  }
}
