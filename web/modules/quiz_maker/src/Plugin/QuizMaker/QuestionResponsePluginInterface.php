<?php

namespace Drupal\quiz_maker\Plugin\QuizMaker;

use Drupal\quiz_maker\QuestionResponseInterface;

/**
 * The Question Response Plugin interface.
 */
interface QuestionResponsePluginInterface extends QuestionResponseInterface {

  /**
   * Get plugin entity.
   *
   * @return \Drupal\quiz_maker\QuestionResponseInterface
   *   The question.
   */
  public function getEntity(): QuestionResponseInterface;

}
