<?php

namespace Drupal\run_workout\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for creating/editing RunWorkout entities.
 */
class RunWorkoutForm extends ContentEntityForm {
    public function buildForm(array $form, FormStateInterface $form_state) {
        $out = parent::buildForm($form, $form_state);
        $out['workoutstep']['#type'] = 'inline_entity_form';
        $out['workoutstep']['#entity_type'] = 'workoutstep';
//        $out['workoutstep']['#type'] = 'inline_entity_complex';


        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state) {
        $entity = $this->entity;

        $status = parent::save($form, $form_state);

        switch ($status) {
            case SAVED_NEW:
                $this->messenger()->addMessage($this->t('Run Workout created: %label.', [
                    '%label' => $entity->label(),
                ]));
                break;

            default:
                $this->messenger()->addMessage($this->t('Run Workout changed: %label.', [
                    '%label' => $entity->label(),
                ]));
        }
        $form_state->setRedirect('entity.runworkout.canonical', ['runworkout' => $entity->id()]);
    }
}
