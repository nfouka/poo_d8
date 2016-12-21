<?php

/**
 * @file
 * Contains \Drupal\mydemo\Controller\Table
 */
namespace Drupal\mydemo\Controller;

use Drupal\Core\Controller\ControllerBase;

class Table extends ControllerBase {
  public function pager() {
    $header = array(
      // We make it sortable by name.
      array('data' => $this->t('Name'), 'field' => 'name', 'sort' => 'asc'),
      array('data' => $this->t('Content')),
    );

    $db = \Drupal::database();
    $query = $db->select('config','c');
    $query->fields('c', array('name'));
    // The actual action of sorting the rows is here.
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
                        ->orderByHeader($header);
    // Limit the rows to 20 for each page.
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                        ->limit(20);
    $result = $pager->execute();

    // Populate the rows.
    $rows = array();
    foreach($result as $row) {
      $rows[] = array('data' => array(
        'name' => $row->name,
        'content' => '[BLOB]', // This hardcoded [BLOB] is just for display purpose only.
      ));
    }

    // The table description.
    $build = array(
      '#markup' => t('List of All Configurations')
    );

    // Generate the table.
    $build['config_table'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    );

    // Finally add the pager.
    $build['pager'] = array(
      '#type' => 'pager'
    );

    return $build;
  }
}
