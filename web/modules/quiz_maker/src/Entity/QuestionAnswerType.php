<?php

namespace Drupal\quiz_maker\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Question Answer type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "question_answer_type",
 *   label = @Translation("Question Answer type"),
 *   label_collection = @Translation("Question Answer types"),
 *   label_singular = @Translation("question answer type"),
 *   label_plural = @Translation("question answers types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count question answers type",
 *     plural = "@count question answers types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\quiz_maker\Form\QuestionAnswerTypeForm",
 *       "edit" = "Drupal\quiz_maker\Form\QuestionAnswerTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\quiz_maker\EntityListBuilder\QuestionAnswerTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer question_answer types",
 *   bundle_of = "question_answer",
 *   config_prefix = "question_answer_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "plugin" = "plugin",
 *   },
 *   links = {
 *     "add-form" = "/admin/quiz-maker/structure/question_answer_types/add",
 *     "edit-form" = "/admin/quiz-maker/structure/question_answer_types/manage/{question_answer_type}",
 *     "delete-form" = "/admin/quiz-maker/structure/question_answer_types/manage/{question_answer_type}/delete",
 *     "collection" = "/admin/quiz-maker/structure/question_answer_types",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "plugin",
 *   },
 * )
 */
final class QuestionAnswerType extends ConfigEntityBundleBase {

  /**
   * The machine name of this question answer type.
   */
  protected string $id;

  /**
   * The human-readable name of the question answer type.
   */
  protected string $label;

  /**
   * The plugin instance ID.
   *
   * @var string
   */
  protected string $plugin;

  /**
   * Get question answer type plugin id.
   *
   * @return ?string
   *   The plugin id.
   */
  public function getPluginId(): ?string {
    if (($plugin_key = $this->getEntityType()->getKey('plugin')) && isset($this->{$plugin_key})) {
      return $this->{$plugin_key};
    }

    return NULL;
  }

}
