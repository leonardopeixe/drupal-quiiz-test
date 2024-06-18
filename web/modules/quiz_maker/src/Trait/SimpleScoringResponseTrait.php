<?php

namespace Drupal\quiz_maker\Trait;

use Drupal\quiz_maker\QuestionInterface;
use Drupal\quiz_maker\QuestionResponseInterface;
use Drupal\quiz_maker\SimpleScoringQuestionInterface;

/**
 * Provides a scoring helper for question answer entity.
 *
 * @internal
 */
trait SimpleScoringResponseTrait {

  /**
   * Implements \Drupal\quiz_maker\SimpleScoringResponseInterface::setScore().
   */
  public function setScore(QuestionInterface $question, bool $value, float $score = NULL, array $response_data = []): QuestionResponseInterface {
    $is_simple_score = $question instanceof SimpleScoringQuestionInterface ? $question->isSimpleScore() : FALSE;
    // When simple scoring disabled, we need to calculate score of every
    // right matching.
    if (!$is_simple_score && $response_data) {
      $answers = $question->getCorrectAnswers() ?? $question->getAnswers();
      $answer_ids = array_map(function ($answer) {
        /** @var \Drupal\quiz_maker\Entity\QuestionAnswer $answer */
        return (int) $answer->id();
      }, $answers);
      $total_score = 0;
      $max_score = 0;
      // Add score for avery guessed matching.
      foreach ($answers as $answer) {
        /** @var \Drupal\quiz_maker\Entity\QuestionAnswer $answer */
        /** @var \Drupal\quiz_maker\SimpleScoringAnswerInterface $answer_instance */
        $answer_instance = $answer->getPluginInstance();
        $max_score += $answer_instance->getScore();
        if (isset($response_data[$answer->id()]) &&
          $this->isResponseCorrect(
            $response_data[$answer->id()],
            $answer->id(),
            $response_data,
            $answer_ids,
          )
        ) {
          $total_score += $answer_instance->getScore();
        }
      }
      $question_max_score = $question->getMaxScore();
      $max_score = $max_score == false ? $total_score : $max_score;
      if($max_score == false) {
        $total_score = 1;
        $max_score = 1;
        $question_max_score = 1;
      }
      // Calculate the fraction from the question max score.
      $total_score = round(($total_score / $max_score) * $question_max_score, 2);
      $result = parent::setScore($question, TRUE, $total_score, $response_data);
    }
    else {
      $result = parent::setScore($question, $value);
    }

    return $result;
  }

}
