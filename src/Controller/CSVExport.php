<?php

namespace Drupal\run_workout\Controller;

use Drupal\Core\Controller\ControllerBase;

class CSVExport extends ControllerBase
{

    public function content()
    {
        $out = "TABLE: workoutstep\n";
        $workoutstepService =  \Drupal::service('run_workout.workoutstep');
        $out .= $workoutstepService->exportStepsCSV();
        $out .= "TABLE: runworkout\n";
        $runworkoutService = \Drupal::service('run_workout.runworkout');
        $out .= $runworkoutService->exportWorkoutsCSV();
        return (new \Symfony\Component\HttpFoundation\Response($out, 200, ['Content-Type' => 'text/csv',
            'Content-Disposition' => 'inline; filename="run_workout.csv"'])
        );
    }
}