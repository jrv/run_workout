<?php

namespace Drupal\run_workout\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\run_workout\WorkoutStep;

/**
 * Form for creating/editing WorkoutStep entities.
 */
class WorkoutStepForm extends ContentEntityForm
{
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $out = parent::buildForm($form, $form_state);
        $out['title']['#states'] = [
            'visible' => [
                'select[name="type"]' => [
                    'value' => 'never'
                ]
            ]
        ];
        # https://www.drupal.org/docs/drupal-apis/form-api/conditional-form-fields
        $out['repeat_times']['#states'] = [
            'visible' => [
                'select[name="type"]' => [
                    'value' => 'repeat'
                ]
            ]
        ];
        $out['repeat_step']['#states'] = [
            'visible' => [
                'select[name="type"]' => [
                    'value' => 'repeat'
                ]
            ]
        ];
        $out['duration']['#states'] = [
            'invisible' => [
                'select[name="type"]' => [
                    'value' => 'repeat'
                ],
            ]
        ];
        $out['intensity']['#states'] = [
            'invisible' => [
                'select[name="type"]' => [
                    'value' => 'repeat'
                ],
            ]
        ];
        $out['duration_time_value']['#states'] = [
            'visible' => [
                [
                    ':input[name="type"]' => ['!value' => 'repeat'],
                    'and',
                    ':input[name="duration"]' => ['value' => 'time'],
                ],
            ],
        ];
        $out['duration_dist_type']['#states'] = [
            'visible' => [
                [
                    ':input[name="type"]' => ['!value' => 'repeat'],
                    'and',
                    ':input[name="duration"]' => ['value' => 'dist'],
                ],
            ],
        ];
        $out['duration_dist_value']['#states'] = [
            'visible' => [
                [
                    ':input[name="type"]' => ['!value' => 'repeat'],
                    'and',
                    ':input[name="duration"]' => ['value' => 'dist'],
                ],
            ],
        ];
        $out['intensity_perc_value']['#states'] = [
            'visible' => [
                [
                    ':input[name="type"]' => ['!value' => 'repeat'],
                    'and',
                    ':input[name="intensity"]' => ['value' => 'perc'],
                ],
            ],
        ];
        $out['intensity_cadence_value']['#states'] = [
            'visible' => [
                [
                    ':input[name="type"]' => ['!value' => 'repeat'],
                    'and',
                    ':input[name="intensity"]' => ['value' => 'cadence'],
                ],
            ],
        ];
        $out['intensity_heartrate_zone']['#states'] = [
            'visible' => [
                [
                    ':input[name="type"]' => ['!value' => 'repeat'],
                    'and',
                    ':input[name="intensity"]' => ['value' => 'heartrate_zone'],
                ],
            ],
        ];

        // enabling this will store an empty workoutstep entry! BUG!
        // $out['repeat_step']['#type'] = 'inline_entity_form';
        // $out['repeat_step']['#entity_type'] = 'workoutstep';

        $out['#cache'] = [
            'max-age' => 0
        ];

        return $out;
    }

    public function getFormId()
    {
        return 'run_workout_workoutstep_form';
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
        // see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21ContentEntityForm.php/function/ContentEntityForm%3A%3AvalidateForm/11.x
        /**
         * @var \Drupal\Core\Entity\ContentEntityInterface $entity
         */
        $entity = $this->buildEntity($form, $form_state);
        $violations = $entity->validate();
        // Remove violations of inaccessible fields.
        $violations->filterByFieldAccess($this->currentUser());
        // In case a field-level submit button is clicked, for example the 'Add
        // another item' button for multi-value fields or the 'Upload' button for a
        // File or an Image field, make sure that we only keep violations for that
        // specific field.
        $edited_fields = [];
        if ($limit_validation_errors = $form_state->getLimitValidationErrors()) {
            foreach ($limit_validation_errors as $section) {
                $field_name = reset($section);
                if ($entity->hasField($field_name)) {
                    $edited_fields[] = $field_name;
                }
            }
            $edited_fields = array_unique($edited_fields);
        } else {
            $edited_fields = $this->getEditedFieldNames($form_state);
        }
//        if ($entity->hasField('intensity_pace_value')) {
//            $form_state->setTemporaryValue('entity_validated', TRUE);
//            return $entity;
//        }
        // Remove violations for fields that are not edited.
        $violations->filterByFields(array_diff(array_keys($entity->getFieldDefinitions()), $edited_fields));
        $this->flagViolations($violations, $form, $form_state);
        // The entity was validated.
        $entity->setValidationRequired(FALSE);
        $form_state->setTemporaryValue('entity_validated', TRUE);
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->entity;
        $status = parent::save($form, $form_state);

        // @todo wis alle velden die niet nodig zijn!
        // Clean up fields based on type
        if ($entity->type->value != 'repeat') {
            $entity->set('repeat_times', NULL);
            $entity->set('repeat_step', NULL);
            if ($entity->duration->value != 'time') {
                $entity->set('duration_time_value', NULL);
            }
            if ($entity->duration->value != 'dist') {
                $entity->set('duration_dist_value', NULL);
                $entity->set('duration_dist_type', NULL);
            }
            if ($entity->intensity->value != 'perc') {
                $entity->set('intensity_perc_value', NULL);
            }
            if ($entity->intensity->value != 'cadence') {
                $entity->set('intensity_cadence_value', NULL);
            }
            if ($entity->intensity->value != 'heartrate_zone') {
                $entity->set('intensity_heartrate_zone', NULL);
            }
        } else {
            $entity->set('duration', NULL);
            $entity->set('duration_time_value', NULL);
            $entity->set('duration_dist_value', NULL);
            $entity->set('duration_dist_type', NULL);
            $entity->set('intensity', NULL);
            $entity->set('intensity_perc_value', NULL);
            $entity->set('intensity_cadence_value', NULL);
            $entity->set('intensity_heartrate_zone', NULL);
        }
        $workoutstepService =  \Drupal::service('run_workout.workoutstep');
        $title = $workoutstepService->getTitle($entity);
        $entity->setTitle($title);
        switch ($status) {
            case SAVED_NEW:
                $this->messenger()->addMessage($this->t('Workout Step created: %label.', [
                    '%label' => $entity->label(),
                ]));
                break;
            default:
                $this->messenger()->addMessage($this->t('Workout Step changed: %label.', [
                    '%label' => $entity->label(),
                ]));
        }
        $entity->save();
        $form_state->setRedirect('entity.workoutstep.canonical', ['workoutstep' => $entity->id()]);
    }

}
