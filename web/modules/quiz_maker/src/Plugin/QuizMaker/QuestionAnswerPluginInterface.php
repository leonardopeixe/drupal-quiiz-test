<?php

namespace Drupal\quiz_maker\Plugin\QuizMaker;

use Drupal\quiz_maker\QuestionAnswerInterface;

/**
 * The Question Plugin interface.
 */
interface QuestionAnswerPluginInterface extends QuestionAnswerInterface {

  /**
   * Get plugin entity.
   *
   * @return \Drupal\quiz_maker\QuestionAnswerInterface
   *   The question.
   */
  public function getEntity(): QuestionAnswerInterface;

}
