<?php

namespace Drupal\run_workout;

use Drupal;

class RunWorkout
{

    public function exportWorkoutsCSV()
    {
        $out = "";
        $fields = array_keys(\Drupal::service('entity_field.manager')->getFieldDefinitions('runworkout', 'test'));
        foreach ($fields as $field) {
            if ($field == 'uuid') continue;
            if ($field == 'metatag') continue;
            if ($field == 'changed') {
                $out .= $field;
                continue;
            }
            if ($field == 'description') {
                $out .= $field . "|description_format|";
                continue;
            }
            $out .= $field . "|";
        }
        $out .= "\n";
        $runworkouts = \Drupal::entityTypeManager()->getStorage('runworkout')->loadMultiple();
        foreach ($runworkouts as $workout) {
            foreach ($fields as $field) {
                switch ($field) {
                    case 'uuid':
                        break;
                    case 'workoutstep':
                        $cnt = $workout->workoutstep->count();
                        if ($cnt > 1) {
                            for ($x = 0; $x < $cnt - 1; $x++) {
                                $out .= $workout->workoutstep[$x]->target_id . "+";
                            }
                            $out .= $workout->workoutstep[$cnt - 1]->target_id;
                        }
                        $out .= "|";
                        break;
                    case 'title':
                    case 'description':
                        if ($workout->{$field}->value != "") {
                            $out .= preg_replace("/\|/", " ", $workout->{$field}->value) . "|";
                        } else {
                            $out .=  "|";
                        }
                        if ($field == 'description') $out .= $workout->{$field}->format . "|";
                        break;
                    case 'metatag':
                        break;
                    case 'changed':
                        $out .= $workout->{$field}->value;
                        break;
                    default:
                        $out .= $workout->{$field}->value . "|";
                }
            }
            $out .= "\n";
        }
        return $out;
    }

    public function importWorkoutsCSV($csv)
    {
        $fields = array_shift($csv);
        for ($x = 0; $x < count($csv) - 1; $x++) {
            $values = array_combine($fields, $csv[$x]);
            $workoutsteps = explode("+", $values['workoutstep']);
            $values['workoutstep'] = $workoutsteps;
            $description = $values['description'];
            $description_format = $values['description_format'];
            unset($values['description_format']);
            $workout = current(\Drupal::entityTypeManager()->getStorage('runworkout')->loadByProperties([
                'id' => $values['id'],
            ]));
            if ($workout) {
                foreach (array_Keys($values) as $field) {
                    $workout->set($field, $values[$field]);
                }
            } else {
                $workout = \Drupal::entityTypeManager()->getStorage('runworkout')->create($values);
            }
            $workout->set('description', ['value' => $description, 'format'=> $description_format]);
            $workout->save();
        }
    }

    public function deleteAll()
    {
        $workouts = \Drupal::entityTypeManager()->getStorage('runworkout')->loadMultiple();
        foreach ($workouts as $workout) {
            $workout->delete();
        }
    }
}
