<?php

namespace Drupal\farm_grazing_plan;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\plan\Entity\PlanInterface;

/**
 * Grazing plan logic.
 */
class GrazingPlan implements GrazingPlanInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getGrazingEvents(PlanInterface $plan): array {
    return $this->entityTypeManager->getStorage('plan_record')->loadByProperties(['plan' => $plan->id(), 'type' => 'grazing_event']);
  }

}
