<?php

namespace Drupal\quiz_maker\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\quiz_maker\Entity\QuestionResponseType;
use Drupal\quiz_maker\Trait\QuizMakerPluginTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for question response type forms.
 */
class QuestionResponseTypeForm extends BundleEntityFormBase {

  use QuizMakerPluginTrait;

  /**
   * Constructs a new \Drupal\quiz_maker\Form\QuestionResponseTypeForm object.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $pluginManager
   *   The plugin manager.
   */
  public function __construct(
    protected PluginManagerInterface $pluginManager,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.quiz_maker.question_response')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\quiz_maker\Entity\QuestionResponseType $response_type */
    $response_type = $this->entity;

    if ($this->operation === 'edit') {
      $form['#title'] = $this->t('Edit %label question response type', ['%label' => $this->entity->label()]);
    }

    $form['label'] = [
      '#title' => $this->t('Label'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->label(),
      '#description' => $this->t('The human-readable name of this question response type.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => [QuestionResponseType::class, 'load'],
        'source' => ['label'],
      ],
      '#description' => $this->t('A unique machine-readable name for this question response type. It must only contain lowercase letters, numbers, and underscores.'),
    ];

    $form['plugin'] = [
      '#title' => $this->t('The response plugin'),
      '#type' => 'select',
      '#options' => $this->getPlugins(),
      '#default_value' => $response_type->getPluginId(),
      '#description' => $this->t('The plugin of this response type.'),
      '#required' => TRUE,
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state): array {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save question response type');
    $actions['delete']['#value'] = $this->t('Delete question response type');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        default => $this->t('The question response type %label has been added.', $message_args),
        SAVED_UPDATED => $this->t('The question response type %label has been updated.', $message_args),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return $result;
  }

}
