<?php

namespace Drupal\jobeet\Entity;

use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Job entities.
 *
 * @ingroup jobeet
 */
interface JobInterface extends RevisionableInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Job name.
   *
   * @return string
   *   Name of the Job.
   */
  public function getName();

  /**
   * Sets the Job name.
   *
   * @param string $name
   *   The Job name.
   *
   * @return \Drupal\jobeet\Entity\JobInterface
   *   The called Job entity.
   */
  public function setName($name);

  /**
   * Gets the Job creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Job.
   */
  public function getCreatedTime();

  /**
   * Sets the Job creation timestamp.
   *
   * @param int $timestamp
   *   The Job creation timestamp.
   *
   * @return \Drupal\jobeet\Entity\JobInterface
   *   The called Job entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Job published status indicator.
   *
   * Unpublished Job are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Job is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Job.
   *
   * @param bool $published
   *   TRUE to set this Job to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\jobeet\Entity\JobInterface
   *   The called Job entity.
   */
  public function setPublished($published);

  /**
   * Gets the Job revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Job revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\jobeet\Entity\JobInterface
   *   The called Job entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Job revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionAuthor();

  /**
   * Sets the Job revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\jobeet\Entity\JobInterface
   *   The called Job entity.
   */
  public function setRevisionAuthorId($uid);

}
