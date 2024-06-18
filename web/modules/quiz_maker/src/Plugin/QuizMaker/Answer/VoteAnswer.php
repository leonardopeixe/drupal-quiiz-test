<?php

namespace Drupal\quiz_maker\Plugin\QuizMaker\Answer;

use Drupal\quiz_maker\Entity\QuestionAnswer;
use Drupal\quiz_maker\Plugin\QuizMaker\QuestionAnswerPluginBase;
use Drupal\quiz_maker\QuestionResponseInterface;

/**
 * Plugin implementation of the question.
 *
 * @QuizMakerQuestionAnswer(
 *   id = "vote_answer",
 *   label = @Translation("Vote answer"),
 *   description = @Translation("Vote answer.")
 * )
 */
class VoteAnswer extends QuestionAnswerPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResponseStatus(QuestionResponseInterface $response): string {
    return QuestionAnswer::NEUTRAL;
  }

}
