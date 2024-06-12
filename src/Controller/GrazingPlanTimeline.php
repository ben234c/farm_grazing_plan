<?php

namespace Drupal\farm_grazing_plan\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\farm_grazing_plan\GrazingPlanInterface;
use Drupal\farm_timeline\TypedData\TimelineRowDefinition;
use Drupal\plan\Entity\PlanInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Grazing plan timeline controller.
 */
class GrazingPlanTimeline extends ControllerBase {

  /**
   * The grazing plan service.
   *
   * @var \Drupal\farm_grazing_plan\GrazingPlanInterface
   */
  protected GrazingPlanInterface $grazingPlan;

  /**
   * Log location service.
   *
   * @var \Drupal\farm_location\LogLocationInterface
   */
  protected $logLocation;

  /**
   * The typed data manager interface.
   *
   * @var \Drupal\Core\TypedData\TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * The serializer service.
   *
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * GrazingPlanTimeline constructor.
   *
   * @param \Drupal\farm_grazing_plan\GrazingPlanInterface $grazing_plan
   *   The grazing plan service.
   * @param Drupal\farm_location\LogLocationInterface $log_location
   *   Log location service.
   * @param \Drupal\Core\TypedData\TypedDataManagerInterface $typed_data_manager
   *   The typed data manager interface.
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   *   The serializer service.
   */
  public function __construct(GrazingPlanInterface $grazing_plan, LogLocationInterface $log_location, TypedDataManagerInterface $typed_data_manager, SerializerInterface $serializer) {
    $this->grazingPlan = $grazing_plan;
    $this->logLocation = $log_location;
    $this->typedDataManager = $typed_data_manager;
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('farm_grazing_plan'),
      $container->get('log.location'),
      $container->get('typed_data_manager'),
      $container->get('serializer'),
    );
  }

  /**
   * API endpoint for grazing plan timeline.
   *
   * @param \Drupal\plan\Entity\PlanInterface $plan
   *   The grazing plan.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Json response of timeline data.
   */
  public function timeline(PlanInterface $plan) {
    $data = [];
    /** @var \Drupal\farm_grazing_plan\Bundle\GrazingEventInterface[] $grazing_events */
    $grazing_events = $this->grazingPlan->getGrazingEvents($plan);
    foreach ($grazing_events as $grazing_event) {

      // Load the grazing event's movement log.
      $log = $grazing_event->getLog();

      // Get the movement log locations.
      $locations = $this->logLocation->getLocation($log);

      // Generate a list of location names.
      $location_names = array_map(function ($location) {
        return $location->label();
      }, $locations);

      // Build initial row values.
      $row_values = [
        'id' => 'grazing-event--' . $grazing_event->id(),
        'link' => Link::fromTextAndUrl(implode(', ', $location_names), $log->toUrl())->toString(),
        'tasks' => [],
      ];

      // Add a task for the grazing event duration.
      $row_values['tasks'][] = [
        'id' => 'grazing-event--duration--' . $grazing_event->id(),
        'start' => $grazing_event->get('start')->value,
        'end' => $grazing_event->get('start')->value + ($grazing_event->get('duration')->value * 60 * 60),
        'meta' => [
          'stage' => 'duration',
        ],
        'classes' => [
          'stage',
          "stage--duration",
        ],
      ];

      // Add a task for the recovery time.
      if (!empty($grazing_event->get('recovery')->value)) {
        $row_values['tasks'][] = [
          'id' => 'grazing-event--recovery--' . $grazing_event->id(),
          'start' => $grazing_event->get('start')->value + ($grazing_event->get('duration')->value * 60 * 60),
          'end' => $grazing_event->get('start')->value + ($grazing_event->get('duration')->value * 60 * 60) + ($grazing_event->get('recovery')->value * 24 * 60 * 60),
          'meta' => [
            'stage' => 'recovery',
          ],
          'classes' => [
            'stage',
            "stage--recovery",
          ],
        ];
      }

      // Add the row object.
      // @todo Create and instantiate a wrapper data type instead of rows.
      $row_definition = TimelineRowDefinition::create('farm_timeline_row');
      $data['rows'][] = $this->typedDataManager->create($row_definition, $row_values);
    }

    // Serialize to JSON and return response.
    $serialized = $this->serializer->serialize($data, 'json');
    return new JsonResponse($serialized, 200, [], TRUE);
  }

}
