<?php

namespace Drupal\quiz_maker;

/**
 * Defines an interface for question answer that have a simple scoring option.
 */
interface SimpleScoringAnswerInterface {

  /**
   * Get answer score.
   *
   * @return ?int
   *   The score.
   */
  public function getScore(): ?int;

}
