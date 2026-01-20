<?php

namespace Drupal\run_workout\Commands;

use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Test extends DrushCommands
{
    /**
     *
     * The RunWorkout Service
     *
     * @var \Drupal\run_workout\RunWorkout
     */
    protected \Drupal\run_workout\RunWorkout $runworkout;

    /**
     * Constructs a RunWorkout Controller.
     */
    public function __construct(\Drupal\run_workout\RunWorkout $runworkout)
    {
        $this->runworkout = $runworkout;
    }

    /**
     *
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static($container->get('run_workout.runworkout'));
    }

    /**
     * Drush command to do tests with run_workout
     *
     * @command runworkout_test
     * @aliases runtest
     * @usage runworkout_test:build
     */
    public function build()
    {
        print "Run Workout Test Hello World!\n\n";
//        $out = "TABLE: workoutsstep\n";
//        $workoutstepService =  \Drupal::service('run_workout.workoutstep');
//        $out .= $workoutstepService->exportStepsCSV();
//        $out .= "TABLE: runnworkout\n";
//        $runworkoutService = \Drupal::service('run_workout.runworkout');
//        $out .= $runworkoutService->exportWorkoutsCSV();
//        print $out;
        $data = [
            'id|title|type|repeat_times|repeat_step|duration|duration_time_value|duration_dist_type|duration_dist_value|intensity|intensity_perc_value|intensity_cadence_value|intensity_heartrate_zone|notes|uid|created|changed',
            '1|WU10:00|warmup|||time|600|||||||rustig warmlopen||1768830779|1768831610',
            '2|5:00@Z3|run|||time|300|||heartrate_zone|||3|||1768831069|1768831069',
            '3|Repeat 3x|repeat|3|5+4|||||||||||1768831083|1768831184',
            '4|R200m|recover|||dist||m|200|||||||1768831108|1768831123',
            '5|600m@Z3|run|||dist||m|600|heartrate_zone|||3|||1768831161|1768831161',
            '6|CDLAP|cooldown|||press||||||||||1768831422|1768831422'
        ];
        $rows = array_map(function($l) { return str_getcsv($l, '|'); }, $data);
        print_r($rows);
        exit;
        $this->runworkout->importWorkoutsCSV($rows);
    }
}