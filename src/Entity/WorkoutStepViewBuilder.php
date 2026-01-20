<?php
namespace Drupal\run_workout\Entity;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * CustomView Builder for WorkoutStep entities.
 */
class WorkoutStepViewBuilder extends EntityViewBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
        parent::buildComponents($build, $entities, $displays, $view_mode);
        foreach ($entities as $id => $entity) {
            if (isset($build[$id]['duration_time_value'])) {
                $build[$id]['duration_time_value'][0]['#format'] = 'm:ss';
            }
            if (isset($build[$id]['intensity_pace_value'])) {
                $build[$id]['intensity_pace_value'][0]['#format'] = 'm:ss';
            }
            if (isset($build[$id]['duration']) && isset($build[$id]['duration'][0]) &&
                ($build[$id]['duration'][0]['#markup'] == 'Distance')) {
                if (isset($build[$id]['duration_dist_value'])) {
                    if ($build[$id]['duration_dist_value'][0]['#markup'] != 'Meter') {
                        $build[$id]['duration_dist_value'][0]['#markup'] = intval($build[$id]['duration_dist_value'][0]['#markup']);
                    } else {
                        $build[$id]['duration_dist_value'][0]['#markup'] = sprintf("%.3f", $build[$id]['duration_dist_value'][0]['#markup']);
                    }
                }
            }
            if (isset($build[$id]['intensity_perc_value']) && (isset($build[$id]['intensity_perc_value'][0]) > 0)) {
                $build[$id]['intensity_perc_value'][0]['#markup'] = $build[$id]['intensity_perc_value'][0]['#markup'] . "%";
            }
        }
    }
}
