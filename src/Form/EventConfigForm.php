<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventConfigForm extends FormBase {

  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('database'));
  }

  public function getFormId() {
    return 'event_registration_admin_config';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => 'Event Name',
      '#required' => TRUE,
    ];

    $form['category'] = [
      '#type' => 'select',
      '#title' => 'Category of the event',
      '#options' => [
        'Online Workshop' => 'Online Workshop',
        'Hackathon' => 'Hackathon',
        'Conference' => 'Conference',
        'One-day Workshop' => 'One-day Workshop',
      ],
      '#required' => TRUE,
    ];

    $form['event_date'] = [
      '#type' => 'date',
      '#title' => 'Event Date',
      '#required' => TRUE,
    ];

    $form['reg_start_date'] = [
      '#type' => 'date',
      '#title' => 'Registration Start Date',
      '#required' => TRUE,
    ];

    $form['reg_end_date'] = [
      '#type' => 'date',
      '#title' => 'Registration End Date',
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Save Event Configuration',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Insert into the event_config table we created in the .install file
    $this->database->insert('event_config')->fields([
      'event_name' => $form_state->getValue('event_name'),
      'category' => $form_state->getValue('category'),
      'event_date' => $form_state->getValue('event_date'),
      'reg_start_date' => $form_state->getValue('reg_start_date'),
      'reg_end_date' => $form_state->getValue('reg_end_date'),
    ])->execute();

    \Drupal::messenger()->addMessage('Event "' . $form_state->getValue('event_name') . '" has been saved.');
  }
}