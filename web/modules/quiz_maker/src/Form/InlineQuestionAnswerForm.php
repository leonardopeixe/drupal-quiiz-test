<?php

namespace Drupal\quiz_maker\Form;

use Drupal\inline_entity_form\Form\EntityInlineForm;

/**
 * Inline entity form for question answer entity.
 */
class InlineQuestionAnswerForm extends EntityInlineForm {

  /**
   * {@inheritdoc}
   */
  public function getTableFields($bundles) {
    $fields = parent::getTableFields($bundles);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function isTableDragEnabled($element) {
    return TRUE;
  }

}
