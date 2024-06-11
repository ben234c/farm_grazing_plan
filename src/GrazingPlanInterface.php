<?php

namespace Drupal\farm_grazing_plan;

use Drupal\plan\Entity\PlanInterface;

/**
 * Grazing plan logic.
 */
interface GrazingPlanInterface {

  /**
   * Get all grazing event plan entity relationship records for a given plan.
   *
   * @param \Drupal\plan\Entity\PlanInterface $plan
   *   The plan entity.
   *
   * @return \Drupal\farm_grazing_plan\Bundle\GrazingEventInterface[]
   *   Returns an array of plan_record entities of type grazing_event.
   */
  public function getGrazingEvents(PlanInterface $plan): array;

}
