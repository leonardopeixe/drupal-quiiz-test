<?php

namespace Drupal\quiz_maker\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\quiz_maker\Annotation\QuizMakerQuestion;
use Drupal\quiz_maker\QuestionInterface;

/**
 * QuizMakerQuestion plugin manager.
 */
final class QuizMakerQuestionPluginManager extends DefaultPluginManager {

  /**
   * Constructs a new QuizMakerQuestionPluginManager object.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/QuizMaker/Question', $namespaces, $module_handler, QuestionInterface::class, QuizMakerQuestion::class);
    $this->alterInfo('question_info');
    $this->setCacheBackend($cache_backend, 'question_plugins');
  }

}
