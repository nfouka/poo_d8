<?php

namespace Drupal\jobeet\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Job entity.
 *
 * @ingroup jobeet
 *
 * @ContentEntityType(
 *   id = "job",
 *   label = @Translation("Job"),
 *   handlers = {
 *     "storage" = "Drupal\jobeet\JobStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\jobeet\JobListBuilder",
 *     "views_data" = "Drupal\jobeet\Entity\JobViewsData",
 *     "translation" = "Drupal\jobeet\JobTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\jobeet\Form\JobForm",
 *       "add" = "Drupal\jobeet\Form\JobForm",
 *       "edit" = "Drupal\jobeet\Form\JobForm",
 *       "delete" = "Drupal\jobeet\Form\JobDeleteForm",
 *     },
 *     "access" = "Drupal\jobeet\JobAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\jobeet\JobHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "job",
 *   data_table = "job_field_data",
 *   revision_table = "job_revision",
 *   revision_data_table = "job_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer job entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/jobeet/indeed/structure/job/{job}",
 *     "add-form" = "/jobeet/indeed/structure/job/add",
 *     "edit-form" = "/jobeet/indeed/structure/job/{job}/edit",
 *     "delete-form" = "/jobeet/indeed/structure/job/{job}/delete",
 *     "version-history" = "/jobeet/indeed/structure/job/{job}/revisions",
 *     "revision" = "/jobeet/indeed/structure/job/{job}/revisions/{job_revision}/view",
 *     "revision_revert" = "/jobeet/indeed/structure/job/{job}/revisions/{job_revision}/revert",
 *     "translation_revert" = "/jobeet/indeed/structure/job/{job}/revisions/{job_revision}/revert/{langcode}",
 *     "revision_delete" = "/jobeet/indeed/structure/job/{job}/revisions/{job_revision}/delete",
 *     "collection" = "/jobeet/indeed/structure/job",
 *   },
 *   field_ui_base_route = "job.settings"
 * )
 */
class Job extends RevisionableContentEntityBase implements JobInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the job owner the
    // revision author.
    if (!$this->getRevisionAuthor()) {
      $this->setRevisionAuthorId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionAuthor() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionAuthorId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Job entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Job entity.'))
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', false)
      ->setDisplayConfigurable('view', false );


     // ADD DEPARTEMENT **********

      $fields['jobeet_departement'] = BaseFieldDefinition::create('list_string')
          ->setLabel(t('Departement'))
          ->setDescription(t('The departement values'))
          ->setSettings(array(
              'allowed_values' => array(
                  'PARIS' => 'PARIS',
                  'MARSEILLE' => 'MARSEILLE',
                  'GRENOBLE' => 'GRENOBLE',
                  'BORDEAUX' => 'BORDEAUX',
                  'LILLE' => 'LILLE' ,
                  'NIMES' => 'NIMES',
                  'AVIGNON' => 'AVIGNON',
                  'POITIER' => 'POITIER',
                  'NANTES' => 'NANTES',
                  'LYON' => 'LYON' ,
                  'MONTPELLIER' => 'MONTPELLIER',
                  'VALANCE' => 'VALANCE',
                  'SAINT-ETIENNE' => 'SAINT-ETIENNE',
                  'ORANGE' => 'ORANGE',
                  'MONTILIMAR' => 'MONTILIMAR' ,
              ),
          ))
          ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'string',
              'weight' => -4,
          ))
          ->setDisplayOptions('form', array(
              'type' => 'options_select',
              'weight' => -4,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

        /////////////////////////////////////////////////////////////////////////////////////



      $fields['jobeet_salary'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Salary'))
          ->setDescription(t('The salary of job.'))
          ->setSettings(array(
              'default_value' => '',
              'max_length' => 255,
              'text_processing' => 0,
          ))
          ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'string',
              'weight' => -6,
          ))
          ->setDisplayOptions('form', array(
              'type' => 'string',
              'weight' => -6,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);


      $fields['start_date'] = BaseFieldDefinition::create('datetime')
          ->setLabel(t('Start date'))
          ->setDescription(t('The date that the survey is started.'))
          ->setSetting('datetime_type', 'date')
          ->setRequired(true)
          ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'string',
              'weight' => -4,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);


      $fields['content'] = BaseFieldDefinition::create('text_long')
          ->setLabel(t('Content'))
          ->setDescription(t('The content of the job'))
          ->setDisplayOptions('form', array(
              'type'   => 'text_textarea',
              'weight' => -6
          ))
          ->setRequired(TRUE)
          ->setDisplayConfigurable('form', TRUE);



      $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Job is published.'))
      ->setRevisionable(false)
      ->setDefaultValue(false);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Revision timestamp'))
      ->setDescription(t('The time that the current revision was created.'))
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Revision user ID'))
      ->setDescription(t('The user ID of the author of the current revision.'))
      ->setSetting('target_type', 'user')
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
