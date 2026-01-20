<?php

namespace Drupal\run_workout\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the WorkoutStep Entity.
 *
 * @ContentEntityType (
 *     id = "workoutstep",
 *     label = @Translation("WorkoutStep"),
 *     label_collection = @Translation("WorkoutSteps"),
 *     label_singular = @Translation("WorkoutStep"),
 *     label_plural = @Translation("WorkoutSteps"),
 *     label_count = @PluralTranslation(
 *       singular = "@count WorkoutStep",
 *       plural = "@count WorkoutSteps",
 *     ),
 *     handlers = {
 *         "views_data" = "Drupal\views\EntityViewsData",
 *         "view_builder" = "Drupal\run_workout\Entity\WorkoutStepViewBuilder",
 *         "list_builder" = "Drupal\run_workout\Entity\WorkoutStepListBuilder",
 *         "form" = {
 *             "default" = "Drupal\run_workout\Form\WorkoutStepForm",
 *             "add" = "Drupal\run_workout\Form\WorkoutStepForm",
 *             "edit" = "Drupal\run_workout\Form\WorkoutStepForm",
 *             "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *             "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *         },
 *         "route_provider" = {
 *             "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *         },
 *     },
 *     base_table = "workoutstep",
 *     admin_permission = "administer site configuration",
 *     entity_keys = {
 *         "id" = "id",
 *         "label" = "title",
 *         "uuid" = "uuid",
 *     },
 *     links = {
 *         "canonical" = "/admin/structure/workoutstep/{workoutstep}",
 *         "add-form" = "/admin/structure/workoutstep/add",
 *         "edit-form" = "/admin/structure/workoutstep/{workoutstep}/edit",
 *         "delete-form" = "/admin/structure/workoutstep/{workoutstep}/delete",
 *         "collection" = "/admin/structure/workoutstep",
 *         "delete-multiple-form" = "/admin/structure/workoutstep/delete-multiple",
 *     },
 * )
 */
class WorkoutStep extends ContentEntityBase implements WorkoutStepInterface
{
    use EntityChangedTrait;

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->get('title')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title = null)
    {
        if ($title == null) {
            $title = $this->get('type');
            if ($this->get('type') != 'repeat') {
                $title .= ' ' . $this->get('duration');
            }
        }
        $this->set('title', $title);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->get('created')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime($timestamp)
    {
        $this->set('created', $timestamp);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);
        $fields['title'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Title'))
            ->setSettings([
                'max_length' => 255,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'string',
                'weight' => -25,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string_textfield',
                'weight' => -25,
            ])
            ->setDisplayConfigurable('form', TRUE);

        $fields['type'] = BaseFieldDefinition::create("list_string")
            ->setSettings([
                'allowed_values' => [
                    'warmup' => 'Warm-up',
                    'run' => 'Run',
                    'recover' => 'Active Recovery',
                    'rest' => 'Rest',
                    'cooldown' => 'Cooldown',
                    'other' => 'Other',
                    'repeat' => 'Repeat']
            ])
            ->setLabel('Type')
            ->setDescription('What kind of step is this?')
            ->setRequired(TRUE)
            ->setDefaultValue('run')
            ->setDisplayOptions('form', array(
                'type' => 'options_select',
            ))
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('form', TRUE);

        $fields['repeat_times'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Repeat'))
            ->setDescription(t('Repeat times'))
            ->setRequired(FALSE)
            ->setDefaultValue(3)
            ->setSettings([
                'min' => 0,
                'max' => 30,
            ])
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayOptions('form', [
                'type' => 'number',
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayConfigurable('form', TRUE);

        $fields['repeat_step'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Step'))
            ->setDescription(t('Steps in this repeat'))
            ->setSettings(['target_type' => 'workoutstep'])
            ->setRequired(FALSE)
            ->setCardinality(-1)
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'entity_reference_label',
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayOptions('form', [
                'type' => 'inline_entity_form' ])
            ->setDisplayConfigurable('form', TRUE);

        # https://www.lullabot.com/articles/module-monday-hms-field
        # Instead of storing an absolute date and time, HMS Field (shortfor 'Hours, minutes, and seconds')
        # stores a simple integer representing a number of seconds.

        $fields['duration'] = BaseFieldDefinition::create("list_string")
            ->setSettings([
                'allowed_values' => [
                    'time' => 'Time',
                    'dist' => 'Distance',
                    'press' => 'Lap Button Press']
            ])
            ->setLabel('Duration')
            ->setRequired(FALSE)
            ->setCardinality(1)
            ->setDescription('Determine type of duration')
            ->setDefaultValue('time')
            ->setDisplayOptions('form', array(
                'type' => 'options_select',
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayConfigurable('view', TRUE);

        $fields['duration_time_value'] = BaseFieldDefinition::create('hms')
            ->setLabel(t('Time'))
            ->setDescription(t('Step time (m:ss)'))
            ->setRequired(FALSE)
            ->setDisplayOptions('form', [
                'type' => 'hms_default',
                'settings' => ['format' => 'm:ss', 'leading_zero' => false],
            ])
            ->setDisplayOptions('view', [
                'type' => 'integer',
                'settings' => ['format' => 'mm:ss', 'leading_zero' => false],
            ])
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['duration_dist_type'] = BaseFieldDefinition::create('list_string')
            ->setLabel(t('Distance type'))
            ->setDescription(t('Distance displayed as m/km'))
            ->setSettings([
                'allowed_values' => ['m' => 'Meter', 'km' => 'Kilometer']
            ])
            ->setRequired(FALSE)
            ->setDefaultValue('m')
            ->setDisplayOptions('form', array(
                'type' => 'options_select',
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayConfigurable('view', TRUE);

        $fields['duration_dist_value'] = BaseFieldDefinition::create('float')
            ->setLabel(t('Distance'))
            ->setDescription(t('Distance in meter or kilometer'))
            ->setRequired(FALSE)
            ->setSettings([
                'min' => 0,
                'max' => 1000,
            ])
            ->setDefaultValue(150)
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'number_decimal',
            ])
            ->setDisplayOptions('form', [
                'type' => 'number',
                'step' => 1,
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayConfigurable('form', TRUE);

        $fields['intensity'] = BaseFieldDefinition::create("list_string")
            ->setSettings([
                'allowed_values' => [
                    'perc' => 'Percentage of 10K pace',
                    'cadence' => 'Cadence',
                    'heartrate_zone' => 'Heart Rate Zone',
                ]
            ])
            ->setRequired(FALSE)
            ->setLabel('Intensity')
            ->setDescription('Step intensity')
            ->setDefaultValue(NULL)
            ->setDisplayOptions('form', array(
                'type' => 'options_select',
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayConfigurable('view', TRUE);

        $fields['intensity_perc_value'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('10K Percentage'))
            ->setDescription(t('Percentage of 10K pace'))
            ->setRequired(FALSE)
            ->setDefaultValue(94)
            ->setSettings([
                'min' => 0,
                'max' => 150,
            ])
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayOptions('form', [
                'type' => 'number',
                'step' => 1,
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayConfigurable('form', TRUE);

        $fields['intensity_cadence_value'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Cadence'))
            ->setDescription(t('Running cadence'))
            ->setRequired(FALSE)
            ->setDefaultValue(180)
            ->setSettings([
                'min' => 0,
                'max' => 250,
            ])
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayOptions('form', [
                'type' => 'number',
                'step' => 1,
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayConfigurable('form', TRUE);

        $fields['intensity_heartrate_zone'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Heartrate Zone'))
            ->setDescription(t('Heartrate Zone'))
            ->setRequired(FALSE)
            ->setDefaultValue(3)
            ->setDefaultValue(3)
            ->setSettings([
                'min' => 1,
                'max' => 5,
            ])
            ->setDisplayOptions('view', [
                'label' => 'above',
            ])
            ->setDisplayOptions('form', [
                'type' => 'number',
                'step' => 1,
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayConfigurable('form', TRUE);

        # https://www.drupal.org/docs/drupal-apis/entity-api/fieldtypes-fieldwidgets-and-fieldformatters
        $fields['notes'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Notes'))
            ->setSettings([
                'max_length' => 255,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'string',
                'weight' => -25,
            ])
            ->setDisplayOptions('form', [
                'type' => 'basic_string',
                'weight' => -2,
            ])
            ->setDisplayConfigurable('form', TRUE);

        $fields['uid'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('User ID'))
            ->setDescription(t('The user ID of the node author.'))
            ->setSettings(array(
                'target_type' => 'user',
                'default_value' => 0,
            ));

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));

        return $fields;
    }
}
