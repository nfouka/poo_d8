<?php

namespace Drupal\jobeet\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\jobeet\Entity\JobInterface;

/**
 * Class JobController.
 *
 *  Returns responses for Job routes.
 *
 * @package Drupal\jobeet\Controller
 */
class JobController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Job  revision.
   *
   * @param int $job_revision
   *   The Job  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($job_revision) {
    $job = $this->entityManager()->getStorage('job')->loadRevision($job_revision);
    $view_builder = $this->entityManager()->getViewBuilder('job');

    return $view_builder->view($job);
  }

  /**
   * Page title callback for a Job  revision.
   *
   * @param int $job_revision
   *   The Job  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($job_revision) {
    $job = $this->entityManager()->getStorage('job')->loadRevision($job_revision);
    return $this->t('Revision of %title from %date', array('%title' => $job->label(), '%date' => format_date($job->getRevisionCreationTime())));
  }

  /**
   * Generates an overview table of older revisions of a Job .
   *
   * @param \Drupal\jobeet\Entity\JobInterface $job
   *   A Job  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(JobInterface $job) {
    $account = $this->currentUser();
    $langcode = $job->language()->getId();
    $langname = $job->language()->getName();
    $languages = $job->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $job_storage = $this->entityManager()->getStorage('job');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $job->label()]) : $this->t('Revisions for %title', ['%title' => $job->label()]);
    $header = array($this->t('Revision'), $this->t('Operations'));

    $revert_permission = (($account->hasPermission("revert all job revisions") || $account->hasPermission('administer job entities')));
    $delete_permission = (($account->hasPermission("delete all job revisions") || $account->hasPermission('administer job entities')));

    $rows = array();

    $vids = $job_storage->revisionIds($job);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\jobeet\JobInterface $revision */
      $revision = $job_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionAuthor(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->revision_timestamp->value, 'short');
        if ($vid != $job->getRevisionId()) {
          $link = $this->l($date, new Url('entity.job.revision', ['job' => $job->id(), 'job_revision' => $vid]));
        }
        else {
          $link = $job->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->revision_log_message->value, '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('job.revision_revert_translation_confirm', ['job' => $job->id(), 'job_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('job.revision_revert_confirm', ['job' => $job->id(), 'job_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('job.revision_delete_confirm', ['job' => $job->id(), 'job_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['job_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );

    return $build;
  }

}
