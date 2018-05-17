<?php

namespace Drupal\write_log\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class WriteLogAdminForm.
 */
class WriteLogAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'write_log.admin',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'write_log_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('write_log.admin');

    $form['anonimous_can_send_contacts'] = array(
      '#type' => 'checkbox',
      '#description' => $this->t('Anonimous users can send contacts'),
      '#title' => $this
        ->t('Send contacts'),
      '#default_value' => $config->get('anonimous_can_send_contacts')
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

    $values = $form_state->getValues();

    $this->config('write_log.admin')
      ->set('anonimous_can_send_contacts', $values['anonimous_can_send_contacts'])
      ->save();
  }

}
