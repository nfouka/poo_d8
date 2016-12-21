<?php

namespace Drupal\tablesort_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for tablesort example routes.
 */
class TableSortExampleController extends ControllerBase {

  /**
   * The Database Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * TableSortExampleController constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * A simple controller method to explain what the tablesort example is about.
   */
  public function description() {
    // We are going to output the results in a table with a nice header.
    $header = array(
      // The header gives the table the information it needs in order to make
      // the query calls for ordering. TableSort uses the field information
      // to know what database column to sort by.
      array('data' => t('Numbers'), 'field' => 't.numbers'),
      array('data' => t('Letters'), 'field' => 't.alpha'),
      array('data' => t('Mixture'), 'field' => 't.random'),
    );

    // Using the TableSort Extender is what tells  the query object that we
    // are sorting.
    $query = $this->database->select('tablesort_example', 't')
      ->extend('Drupal\Core\Database\Query\TableSortExtender');
    $query->fields('t');

    // Don't forget to tell the query object how to find the header information.
    $result = $query
      ->orderByHeader($header)
      ->execute();

    $rows = array();
    $rows2 = array();
    foreach ($result as $row) {
      // Normally we would add some nice formatting to our rows
      // but for our purpose we are simply going to add our row
      // to the array.
      $rows[] = array('data' => (array) $row);

      $rows2[] = array(
          $row->numbers ,
          $row->alpha ,
      ) ;

    }

    // Build the table for the nice output.
    $build = array(
      '#markup' => '<p>' . t('The layout here is a themed as a table
           that is sortable by clicking the header name.') . '</p>',
    );
    $build['tablesort_table'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows2,
    );

    return $build;
  }

}

/*
 *
 * tdClass Object ( [numbers] => 1 [alpha] => e [random] => 912cv21 ) stdClass Object ( [numbers] => 2 [alpha] => a [random] => 0kuykuh ) stdClass Object ( [numbers] => 3 [alpha] => m [random] => fuye8734h ) stdClass Object ( [numbers] => 4 [alpha] => w [random] => 80jsv772 ) stdClass Object ( [numbers] => 5 [alpha] => o [random] => d82sf-csj ) stdClass Object ( [numbers] => 6 [alpha] => s [random] => au832 ) stdClass Object ( [numbers] => 7 [alpha] => e [random] => t982hkv )
Skip to main content
Toolbar items
Manage
Administration menu

    Content
    Structure
    Appearance
    Extend
    Configuration
    People
    Reports
    Help

Shortcuts
root


 */