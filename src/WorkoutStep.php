<?php

namespace Drupal\run_workout;

use Drupal;

class WorkoutStep
{

    public function exportStepsCSV()
    {
        $out = "";
        $fields = array_keys(\Drupal::service('entity_field.manager')->getFieldDefinitions('workoutstep', 'test'));
        foreach ($fields as $field) {
            if ($field == 'uuid') continue;
            if ($field == 'metatag') continue;
            if ($field == 'changed') {
                $out .= $field;
                continue;
            }
            $out .= $field . "|";
        }
        $out .= "\n";
        $workoutteps = \Drupal::entityTypeManager()->getStorage('workoutstep')->loadMultiple();
        foreach ($workoutteps as $step) {
            foreach ($fields as $field) {
                switch ($field) {
                    case 'uuid':
                        break;
                    case 'repeat_step':
                        $cnt = $step->repeat_step->count();
                        if ($cnt > 1) {
                            for ($x = 0; $x < $cnt - 1; $x++) {
                                $out .= $step->repeat_step[$x]->target_id . "+";
                            }
                            $out .= $step->repeat_step[$cnt - 1]->target_id;
                        }
                        $out .= "|";
                        break;
                    case 'notes':
                    case 'title':
                        if ($step->{$field}->value != "") {
                            $out .= preg_replace("/\|/", " ", $step->{$field}->value) . "|";
                        } else {
                            $out .=  "|";
                        }
                        break;
                    case 'intensiteit':
                        if ($step->{$field}->value == 'geen')
                            $out .= "|";
                        else
                            $out .= $step->{$field}->value . "|";
                        break;
                    case 'metatag':
                        break;
                    case 'changed':
                        $out .= $step->{$field}->value;
                        break;
                    default:
                        $out .= $step->{$field}->value . "|";
                }
            }
            $out .= "\n";
        }
        return $out;
    }

    public function importStepCSV($csv)
    {
        $fields = array_shift($csv);
        for ($x = 0; $x < count($csv); $x++) {
            $values = array_combine($fields, $csv[$x]);
            $repeat_steps = explode("+", $values['repeat_step']);
            $values['repeat_step'] = $repeat_steps;
            $step = current(\Drupal::entityTypeManager()->getStorage('workoutstep')->loadByProperties([
                'id' => $values['id'],
            ]));
            if ($step) {
                foreach (array_Keys($values) as $field) {
                    $step->set($field, $values[$field]);
                }
            } else {
                $step = \Drupal::entityTypeManager()->getStorage('workoutstep')->create($values);
            }
            $step->save();
        }
    }

    public function deleteAll()
    {
        $steps = \Drupal::entityTypeManager()->getStorage('workoutstep')->loadMultiple();
        foreach ($steps as $step) {
            $step->delete();
        }
    }

    private function getTitleType($entity)
    {
        $hmsFormatter = \Drupal::service('hms_field.hms');
        switch ($entity->duration->value) {
            case 'time':
                $seconds = $entity->duration_time_value->value;
                $title = $hmsFormatter->secondsToFormatted($seconds, 'm:ss');
                break;
            case 'dist':
                switch ($entity->duration_dist_type->value) {
                    case 'm':
                        $title = intval($entity->duration_dist_value->value) . 'm';
                        break;
                    case 'km':
                        $title = sprintf("%.3f", $entity->duration_dist_value->value) . 'km';
                        break;
                }
                break;
            case 'press':
                $title = 'LAP';
                break;
        }
        return $title;
    }

    private function getTitleIntensity($entity)
    {
        $hmsFormatter = \Drupal::service('hms_field.hms');
        switch ($entity->intensity->value) {
            case 'perc':
                $val = $entity->intensity_perc_value->value;
                $title = '@' . $val . '%';
                break;
            case 'cadans':
                $val = $entity->intensiteit_cadence_value->value;
                $title = '@' . $val . 'spm';
                break;
            case 'heartrate_zone':
                $title = '@Z' . $entity->intensity_heartrate_zone->value;
                break;
            default:
                $title = '';
        }
        return $title;
    }

    public function getTitle($entity)
    {
        // Generate title based on entity type and values
        switch ($entity->type->value) {
            case 'warmup':
                $title = 'WU' . $this->getTitleType($entity) . $this->getTitleIntensity($entity);
                break;
            case 'run':
                $title = $this->getTitleType($entity) . $this->getTitleIntensity($entity);
                break;
            case 'recover':
                $title = 'R' . $this->getTitleType($entity) . $this->getTitleIntensity($entity);
                break;
            case 'rest':
                $title = 'R' . $this->getTitleType($entity);
                break;
            case 'cooldown':
                $title = 'CD' . $this->getTitleType($entity) . $this->getTitleIntensity($entity);
                break;
            case 'other':
                $title = $entity->notes->value;
                break;
            case 'repeat':
                $title = 'Repeat';
                $title .= ' ' . $entity->repeat_times->value . 'x';
                break;
            default:
                $title = 'Broken';
        }

        return $title;

    }
}
