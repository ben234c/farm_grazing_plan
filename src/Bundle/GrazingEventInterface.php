<?php

namespace Drupal\farm_grazing_plan\Bundle;

use Drupal\log\Entity\LogInterface;
use Drupal\plan\Entity\PlanRecordInterface;

/**
 * Bundle logic for Grazing Event.
 */
interface GrazingEventInterface extends PlanRecordInterface {

  /**
   * Returns the grazing event's movement log.
   *
   * @return \Drupal\log\Entity\LogInterface|null
   *   The log entity or NULL if not assigned.
   */
  public function getLog(): ?LogInterface;

}
