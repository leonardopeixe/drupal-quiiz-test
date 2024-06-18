<?php

namespace Drupal\quiz_maker\Plugin\QuizMaker\Response;

use Drupal\quiz_maker\Plugin\QuizMaker\QuestionResponsePluginBase;

/**
 * Plugin implementation of the question.
 *
 * @QuizMakerQuestionResponse(
 *    id = "vote_response",
 *    label = @Translation("Vote response"),
 *    description = @Translation("Vote response.")
 * )
 */
class VoteResponse extends QuestionResponsePluginBase {
  /**
   * {@inheritDoc}
   */
  public function isResponseCorrect(): bool {
    return TRUE;
  }

}
