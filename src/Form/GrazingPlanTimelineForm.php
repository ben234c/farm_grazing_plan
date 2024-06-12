<?php

namespace Drupal\farm_grazing_plan\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\farm_grazing_plan\GrazingPlanInterface;
use Drupal\plan\Entity\PlanInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Grazing plan form.
 */
class GrazingPlanTimelineForm extends FormBase {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The grazing plan service.
   *
   * @var \Drupal\farm_grazing_plan\GrazingPlanInterface
   */
  protected GrazingPlanInterface $grazingPlan;

  /**
   * GrazingPlanTimelineForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\farm_grazing_plan\GrazingPlanInterface $grazing_plan
   *   The grazing plan service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, GrazingPlanInterface $grazing_plan) {
    $this->entityTypeManager = $entity_type_manager;
    $this->grazingPlan = $grazing_plan;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('farm_grazing_plan'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_grazing_plan_timeline_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $plan = NULL) {

    // If a plan is not available, bail.
    if (empty($plan) || !($plan instanceof PlanInterface) || $plan->bundle() != 'grazing') {
      return [
        '#type' => 'markup',
        '#markup' => 'No grazing plan was provided.',
      ];
    }

    // Render the timeline.
    $form['timeline'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'id' => 'timeline',
        'data-table-header' => $this->t('Grazing events'),
        'data-timeline-url' => 'plan/' . $plan->id() . '/timeline',
        'data-timeline-instantiator' => 'farm_grazing_plan',
      ],
      '#attached' => [
        'library' => ['farm_grazing_plan/timeline'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
