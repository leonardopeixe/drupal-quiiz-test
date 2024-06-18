<?php

namespace Drupal\quiz_maker\Plugin\QuizMaker;

use Drupal\quiz_maker\QuestionInterface;

/**
 * The Question Plugin interface.
 */
interface QuestionPluginInterface extends QuestionInterface {

  /**
   * Get plugin entity.
   *
   * @return \Drupal\quiz_maker\QuestionInterface
   *   The question.
   */
  public function getEntity(): QuestionInterface;

}
