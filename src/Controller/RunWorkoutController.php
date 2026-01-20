<?php

namespace Drupal\run_workout\Controller;

use Drupal\Core\Controller\ControllerBase;

class RunWorkoutController extends ControllerBase
{

    public function content()
    {
        $out['info'] = [
          '#type' => 'markup',
          '#markup' => '<h3>Run Workouts</h3>'
        ];

        $out['info2'] = [
          '#type' => 'markup',
          '#markup' => '<p><a href="/admin/structure/runworkout">RunWorkouts list</p>'
        ];
        $out['info3'] = [
            '#type' => 'markup',
            '#markup' => '<p><a href="/admin/structure/workoutstep">Workout Steps list</p>'
        ];

        $out['info4'] = [
            '#type' => 'markup',
            '#markup' => '<p><a href="/admin/run_workout_csvexport">Run Workout CSV Export</p>'

        ];

        $out['info5'] = [
            '#type' => 'markup',
            '#markup' => '<p><a href="/admin/run_workout_csvimport">Run Workout CSV Import</p>'

        ];

        return $out;
    }
}