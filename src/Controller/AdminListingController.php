<?php

namespace Drupal\event_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class AdminListingController extends ControllerBase {

  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('database'));
  }

  public function content() {
    $header = ['ID', 'Name', 'Email', 'Event Name', 'Date Submitted'];
    $rows = [];

    $query = $this->database->select('event_registrations', 'r')->fields('r')->execute();
    foreach ($query as $record) {
      $rows[] = [
        $record->id,
        $record->full_name,
        $record->email,
        $record->event_name,
        date('Y-m-d', $record->created),
      ];
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => 'No registrations yet.',
    ];

    return $build;
  }
}