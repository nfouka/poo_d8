<?php

namespace Drupal\jobeet\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Job entities.
 */
class JobViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['job']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Job'),
      'help' => $this->t('The Job ID.'),
    );

    return $data;
  }

}
