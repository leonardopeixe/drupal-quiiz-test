<?php

namespace Drupal\quiz_maker\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\entity\Form\RevisionRevertForm;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting a question revision for a single translation.
 *
 * @internal
 */
class QuestionRevisionRevertTranslationForm extends RevisionRevertForm {

  /**
   * The langcode.
   *
   * @var mixed|null
   */
  private mixed $langcode;

  /**
   * Constructs a new QuestionRevisionRevertTranslationForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $questionStorage
   *   The quiz storage.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundle_information
   *   The bundle info service.
   */
  public function __construct(
    protected EntityStorageInterface $questionStorage,
    protected DateFormatterInterface $date_formatter,
    protected LanguageManagerInterface $languageManager,
    protected TimeInterface $time,
    protected EntityTypeBundleInfoInterface $bundle_information,
  ) {
    parent::__construct($date_formatter, $bundle_information);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('question'),
      $container->get('date.formatter'),
      $container->get('language_manager'),
      $container->get('datetime.time'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'question_revision_revert_translation_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    /** @var \Drupal\Core\Entity\RevisionLogInterface $revision */
    $revision = $this->revision;
    return $this->t(
      'Are you sure you want to revert @language translation to the revision from %revision-date?',
      [
        '@language' => $this->languageManager->getLanguageName($this->langcode),
        '%revision-date' => $this->dateFormatter->format($revision->getRevisionCreationTime()),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This action cannot be undone.');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $question_revision = NULL, $langcode = NULL) {
    $this->langcode = $langcode;
    $form = parent::buildForm($form, $form_state, $question_revision);
    /** @var \Drupal\Core\Entity\TranslatableRevisionableInterface $revision */
    $revision = $this->revision;

    // Unless untranslatable fields are configured to affect only the default
    // translation, we need to ask the user whether they should be included in
    // the revert process.
    $default_translation_affected = $revision->isDefaultTranslationAffectedOnly();
    $form['revert_untranslated_fields'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Revert content shared among translations'),
      '#default_value' => $default_translation_affected && $revision->getTranslation($this->langcode)->isDefaultTranslation(),
      '#access' => !$default_translation_affected,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareRevertedRevision(NodeInterface $revision, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Entity\RevisionableStorageInterface $storage */
    $storage = $this->questionStorage;
    $translation = $revision->getTranslation($this->langcode);
    return $storage->createRevision($translation, TRUE);
  }

}
