<?php

namespace Drupal\quiz_maker\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Question Response type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "question_response_type",
 *   label = @Translation("Question Response type"),
 *   label_collection = @Translation("Question Response types"),
 *   label_singular = @Translation("question response type"),
 *   label_plural = @Translation("question responses types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count question responses type",
 *     plural = "@count question responses types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\quiz_maker\Form\QuestionResponseTypeForm",
 *       "edit" = "Drupal\quiz_maker\Form\QuestionResponseTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\quiz_maker\EntityListBuilder\QuestionResponseTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer question_response types",
 *   bundle_of = "question_response",
 *   config_prefix = "question_response_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "plugin" = "plugin",
 *   },
 *   links = {
 *     "add-form" = "/admin/quiz-maker/structure/question_response_types/add",
 *     "edit-form" = "/admin/quiz-maker/structure/question_response_types/manage/{question_response_type}",
 *     "delete-form" = "/admin/quiz-maker/structure/question_response_types/manage/{question_response_type}/delete",
 *     "collection" = "/admin/quiz-maker/structure/question_response_types",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "plugin",
 *   },
 * )
 */
final class QuestionResponseType extends ConfigEntityBundleBase {

  /**
   * The machine name of this question response type.
   */
  protected string $id;

  /**
   * The human-readable name of the question response type.
   */
  protected string $label;

  /**
   * The plugin instance ID.
   *
   * @var string
   */
  protected string $plugin;

  /**
   * Get question response type plugin id.
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
