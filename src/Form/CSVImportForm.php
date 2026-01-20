<?php

namespace Drupal\run_workout\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\File\FileSystemInterface;

class CSVImportForm extends FormBase
{
    /**
     *
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'run_workout.csvimportform';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['delete'] = [
            '#type' => 'checkboxes',
            '#options' => [
                'delete' => $this->t('Delete existing run workouts')
            ],
            '#title' => $this->t('Wissen'),
            '#required' => FALSE
        ];

        $form['csvfile'] = [
            '#type' => 'file',
            '#title' => $this->t('Run Workouts CSV File'),
            '#upload_validators' => [
                'file_validate_extensions' => ['csv'],
                '#description' => $this->t('Your CSV (.csv file)').': '.\Drupal::state()->get('csvfile'),
            ]
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Submit'),
            '#submit' => [
                '::submitForm'
            ]
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $workoutStep = new \Drupal\run_workout\WorkoutStep();
        $runWorkout = new \Drupal\run_workout\RunWorkout();
        $delete = $form_state->getValue('delete', false);
        if ($delete) {
            $workoutStep->deleteAll();
            $runWorkout->deleteAll();
        }
        $validators = [
            'file_validate_extensions' => ['csv'],
        ];

        if ($file = file_save_upload('csvfile', $validators, 'temporary://')) {
            $data = file_get_contents($file[0]->getFileUri());
            $data = preg_split("/[\r\n]+/", $data);
            $rows = array_map(function($l) { return str_getcsv($l, '|'); }, $data);
            $line = array_shift($rows);
            if ($line[0] == "TABLE: workoutstep") {
                $steps = [];
                while ($rows) {
                    $step = array_shift($rows);
                    if ($step[0] == "TABLE: runworkout") break;
                    $steps[] = $step;
                }
                $workoutStep->importStepCSV($steps);
                $workouts = [];
                while ($rows) {
                    $workout = array_shift($rows);
                    $workouts[] = $workout;
                }
                $runWorkout->importWorkoutsCSV($workouts);
                \Drupal::messenger()->addMessage($this->t('File uploaded and processed successfully.'));
            } else {
                \Drupal::messenger()->addError($this->t('File read error.'));
            }
        } else {
            \Drupal::messenger()->addError($this->t('File upload failed.'));
        }
    }

}