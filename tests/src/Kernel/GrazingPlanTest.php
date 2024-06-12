<?php

namespace Drupal\Tests\farm_grazing_plan\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\farm_grazing_plan\Traits\MockGrazingPlanEntitiesTrait;

/**
 * Tests for farmOS grazing plan.
 *
 * @group farm_grazing_plan
 */
class GrazingPlanTest extends KernelTestbase {

  use MockGrazingPlanEntitiesTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'asset',
    'entity',
    'farm_activity',
    'farm_animal',
    'farm_animal_type',
    'farm_entity',
    'farm_grazing_plan',
    'farm_id_tag',
    'farm_field',
    'farm_land',
    'farm_location',
    'farm_log',
    'farm_log_asset',
    'farm_map',
    'geofield',
    'log',
    'options',
    'plan',
    'state_machine',
    'taxonomy',
    'text',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('asset');
    $this->installEntitySchema('log');
    $this->installEntitySchema('plan');
    $this->installEntitySchema('plan_record');
    $this->installEntitySchema('taxonomy_term');
    $this->installEntitySchema('user');
    $this->installConfig([
      'farm_grazing_plan',
      'farm_land',
      'farm_animal',
      'farm_animal_type',
      'farm_activity',
    ]);
  }

  /**
   * Test the grazing plan service.
   */
  public function testGrazingPlanService() {

    // Create mock plan entities.
    $this->createMockPlanEntities();

    // Test getting all grazing_event plan_record entities for a plan.
    $grazing_events = \Drupal::service('farm_grazing_plan')->getGrazingEvents($this->plan);
    $this->assertCount(5, $grazing_events);
    usort($grazing_events, function ($a, $b) {
      return ($a->getLog()->id() < $b->getLog()->id()) ? -1 : 1;
    });
    foreach ($grazing_events as $delta => $grazing_event) {
      $this->assertEquals($this->movementLogs[$delta]->id(), $grazing_event->getLog()->id());
      $this->assertEquals($this->movementLogs[$delta]->get('timestamp')->value, $grazing_event->get('start')->value);
      $this->assertEquals(168, $grazing_event->get('duration')->value);
      $this->assertEquals(360, $grazing_event->get('recovery')->value);
    }
  }

}
