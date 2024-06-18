<?php

namespace Drupal\quiz_maker\Trait;

use Drupal\quiz_maker\Entity\Question;

/**
 * Provides a scoring helper for question entity.
 *
 * @internal
 */
trait SimpleScoringQuestionTrait {

  /**
   * Implements SimpleScoringQuestionInterface::isSimpleScore().
   */
  public function isSimpleScore(): bool {
    if ($this instanceof Question && $this->hasField('field_simple_scoring')) {
      return (bool) $this->get('field_simple_scoring')->getString();
    }
    return FALSE;
  }

}
