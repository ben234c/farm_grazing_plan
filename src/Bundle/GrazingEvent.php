<?php

namespace Drupal\farm_grazing_plan\Bundle;

use Drupal\log\Entity\LogInterface;
use Drupal\plan\Entity\PlanRecord;

/**
 * Bundle logic for Grazing Event.
 */
class GrazingEvent extends PlanRecord implements GrazingEventInterface {

  /**
   * {@inheritdoc}
   */
  public function getLog(): ?LogInterface {
    return $this->get('log')->first()?->entity;
  }

}
