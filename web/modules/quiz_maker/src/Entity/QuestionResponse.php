<?php

namespace Drupal\quiz_maker\Entity;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\quiz_maker\Plugin\QuizMaker\QuestionResponsePluginInterface;
use Drupal\quiz_maker\QuestionInterface;
use Drupal\quiz_maker\QuestionResponseInterface;
use Drupal\quiz_maker\QuizInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the question response entity class.
 *
 * @ContentEntityType(
 *   id = "question_response",
 *   label = @Translation("Question Response"),
 *   label_collection = @Translation("Question Responses"),
 *   label_singular = @Translation("question response"),
 *   label_plural = @Translation("question responses"),
 *   label_count = @PluralTranslation(
 *     singular = "@count question responses",
 *     plural = "@count question responses",
 *   ),
 *   bundle_label = @Translation("Question Response type"),
 *   handlers = {
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\quiz_maker\Form\QuestionResponseForm",
 *       "edit" = "Drupal\quiz_maker\Form\QuestionResponseForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler",
 *   },
 *   base_table = "question_response",
 *   data_table = "question_response_field_data",
 *   admin_permission = "administer question_response types",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "bundle",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/quiz-maker/question-response",
 *     "add-form" = "/question-response/add/{question_response_type}",
 *     "add-page" = "/question-response/add",
 *     "canonical" = "/question-response/{question_response}",
 *     "edit-form" = "/question-response/{question_response}/edit",
 *     "delete-form" = "/question-response/{question_response}/delete",
 *     "delete-multiple-form" = "/admin/quiz-maker/question-response/delete-multiple",
 *   },
 *   bundle_entity_type = "question_response_type",
 *   field_ui_base_route = "entity.question_response_type.edit_form",
 * )
 */
class QuestionResponse extends ContentEntityBase implements QuestionResponseInterface, ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['is_correct'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is correct'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Is correct')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(self::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['question_id'] = BaseFieldDefinition::create('entity_reference_revisions')
      ->setLabel(t('Question'))
      ->setSetting('target_type', 'question')
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'weight' => 4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['quiz_id'] = BaseFieldDefinition::create('entity_reference_revisions')
      ->setLabel(t('Quiz'))
      ->setSetting('target_type', 'quiz')
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['responses'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Chosen answers'))
      ->setSetting('target_type', 'question_answer')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'weight' => 6,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['score'] = BaseFieldDefinition::create('float')
      ->setLabel('Score')
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'score_field_formatter',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the question response was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 8,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 8,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the question response was last edited.'));

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function getQuestion(): ?QuestionInterface {
    return $this->getPluginInstance()?->getQuestion();
  }

  /**
   * {@inheritDoc}
   */
  public function getQuiz(): ?QuizInterface {
    return $this->getPluginInstance()?->getQuiz();
  }

  /**
   * {@inheritDoc}
   */
  public function isCorrect(): bool {
    return $this->getPluginInstance()?->isCorrect();
  }

  /**
   * {@inheritDoc}
   */
  public function getScore(): float {
    return $this->getPluginInstance()?->getScore();
  }

  /**
   * {@inheritDoc}
   */
  public function setResponseData(array $data): QuestionResponseInterface {
    return $this->getPluginInstance()?->setResponseData($data);
  }

  /**
   * {@inheritDoc}
   */
  public function setCorrect(bool $value): QuestionResponseInterface {
    return $this->getPluginInstance()?->setCorrect($value);
  }

  /**
   * {@inheritDoc}
   */
  public function setQuiz(QuizInterface $quiz): QuestionResponseInterface {
    return $this->getPluginInstance()?->setQuiz($quiz);
  }

  /**
   * {@inheritDoc}
   */
  public function setQuestion(QuestionInterface $question): QuestionResponseInterface {
    return $this->getPluginInstance()?->setQuestion($question);
  }

  /**
   * {@inheritDoc}
   */
  public function setScore(QuestionInterface $question, bool $value, float $score = NULL, array $response_data = []): QuestionResponseInterface {
    return $this->getPluginInstance()?->setScore($question, $value, $score, $response_data);
  }

  /**
   * {@inheritDoc}
   */
  public function getResponses(): array {
    return $this->getPluginInstance()?->getResponses();
  }

  /**
   * {@inheritDoc}
   */
  public function setFeedback(string $feedback_string): QuestionResponseInterface {
    return $this->getPluginInstance()?->setFeedback($feedback_string);
  }

  /**
   * {@inheritDoc}
   */
  public function getFeedback(): ?string {
    return $this->getPluginInstance()?->getFeedback();
  }

  /**
   * Get response plugin instance.
   *
   * @return ?\Drupal\quiz_maker\Plugin\QuizMaker\QuestionResponsePluginInterface
   *   The plugin instance.
   */
  public function getPluginInstance(): ?QuestionResponsePluginInterface {
    $response_type = QuestionResponseType::load($this->bundle());
    if ($response_type instanceof QuestionResponseType) {
      /** @var \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager */
      $plugin_manager = \Drupal::service('plugin.manager.quiz_maker.question_response');
      try {
        $response_instance = $plugin_manager->createInstance($response_type->getPluginId(), ['response' => $this]);
        return $response_instance instanceof QuestionResponsePluginInterface ? $response_instance : NULL;
      }
      catch (PluginException $e) {
        \Drupal::logger('quiz_maker')->error($e->getMessage());
        return NULL;
      }
    }

    return NULL;
  }

}
