<?php

namespace Drupal\jobeet\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class JobeetController.
 *
 * @package Drupal\jobeet\Controller
 */
class JobeetController extends ControllerBase {


    /**
     * The Database Connection.
     *
     * @var \Drupal\Core\Database\Connection
     */
    protected $database;

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
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('database')
        );
    }



    /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): $name'),
    ];
  }

  public function validate($id){

        db_query('UPDATE `indeed`.`job_field_data` SET `status` = \'1\' WHERE `job_field_data`.`id` ='.$id ) ;
        drupal_flush_all_caches() ;

        $absolute_url = 'http://127.0.0.1:8088/jobs-view-list2' ;
        return new RedirectResponse($absolute_url);
  }

  public function refresh(){
      drupal_flush_all_caches() ;
      $absolute_url = 'http://127.0.0.1:8088/jobs-view-list2' ;
      return new RedirectResponse($absolute_url);
  }

  public function getJobById() {

      $header = array(
          array('data' => t('id'),          'field' => 't.jobeet_departement'),
          array('data' => t('Ref'),         'field' => 't.jobeet_salary'),
          array('data' => t('langcode'),    'field' => 't.name'),
          array('data' => t('langcode'),    'field' => 't.content__value'),
      );

      $query = $this->database->select('job_field_data', 't')
          ->extend('Drupal\Core\Database\Query\TableSortExtender');
      $query->fields('t');

      $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
          ->orderByHeader($header);
      // Limit the rows to 20 for each page.
      $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
          ->limit(5);
      $result = $pager->execute();
      $rows = array();
      $rows2 = array();

      foreach ($result as $row) {
          $rows[] = array('data' => (array) $row);
          $rows2[] = array(
              $row->jobeet_departement ,
              $row->jobeet_salary ,
              $row->name ,
              $row->content__value
          ) ;
      }

      // The table description.
      $build = array(
          '#markup' => t('List of All Configurations')
      );

      // Generate the table.
      $build['tablesort_table2'] = array(
          '#theme' => 'table',
          '#header' => $header,
          '#rows' => $rows2,
      );

      // Finally add the pager.
      $build['pager'] = array(
          '#type' => 'pager'
      );

      return $build;
  }

}
