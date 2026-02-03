<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Mail\MailManagerInterface;

class RegistrationForm extends FormBase {

  protected $database;
  protected $mailManager;

  // Dependency Injection: Injecting Database and Mail services
  public function __construct(Connection $database, MailManagerInterface $mail_manager) {
    $this->database = $database;
    $this->mailManager = $mail_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('plugin.manager.mail')
    );
  }

  public function getFormId() {
    return 'event_registration_user_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => 'Full Name',
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => 'Email Address',
      '#required' => TRUE,
    ];

    // Category dropdown triggers AJAX to update Event Names
    $form['category'] = [
      '#type' => 'select',
      '#title' => 'Category of the event',
      '#options' => [
        '' => '- Select Category -',
        'Online Workshop' => 'Online Workshop',
        'Hackathon' => 'Hackathon',
        'Conference' => 'Conference',
        'One-day Workshop' => 'One-day Workshop',
      ],
      '#ajax' => [
        'callback' => '::updateEventOptions',
        'wrapper' => 'event-details-wrapper',
      ],
    ];

    $form['event_details_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-details-wrapper'],
    ];

    // Logic to populate events based on category selection
    $selected_category = $form_state->getValue('category');
    if ($selected_category) {
      $events = $this->database->select('event_config', 'e')
        ->fields('e', ['event_name', 'event_date'])
        ->condition('category', $selected_category)
        ->execute()
        ->fetchAll();

      $options = [];
      foreach ($events as $event) {
        $options[$event->event_name] = $event->event_name . ' (' . $event->event_date . ')';
      }

      $form['event_details_wrapper']['event_name'] = [
        '#type' => 'select',
        '#title' => 'Available Events',
        '#options' => $options,
        '#required' => TRUE,
      ];
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Register',
    ];

    return $form;
  }

  // AJAX Callback function
  public function updateEventOptions(array &$form, FormStateInterface $form_state) {
    return $form['event_details_wrapper'];
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    $event_name = $form_state->getValue('event_name');

    // Prevent duplicate registration for the same email and event
    $duplicate = $this->database->select('event_registrations', 'r')
      ->fields('r', ['id'])
      ->condition('email', $email)
      ->condition('event_name', $event_name)
      ->execute()
      ->fetchField();

    if ($duplicate) {
      $form_state->setErrorByName('email', 'You have already registered for this event.');
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save to custom database table
    $this->database->insert('event_registrations')->fields([
      'full_name' => $form_state->getValue('full_name'),
      'email' => $form_state->getValue('email'),
      'event_name' => $form_state->getValue('event_name'),
      'created' => time(),
    ])->execute();

    \Drupal::messenger()->addMessage('Registration submitted successfully!');
  }
}