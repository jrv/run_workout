<?php

namespace Drupal\run_workout\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;


/**
 * Defines the RunWorkout Entity.
 *
 * @ContentEntityType (
 *     id = "runworkout",
 *     label = @Translation("RunWorkout"),
 *     label_collection = @Translation("RunWorkouts"),
 *     label_singular = @Translation("runworkout"),
 *     label_plural = @Translation("runworkouts"),
 *     label_count = @PluralTranslation(
 *       singular = "@count runworkout",
 *       plural = "@count runworkouts",
 *     ),
 *     handlers = {
 *         "views_data" = "Drupal\views\EntityViewsData",
 *         "view_builder" = "Drupal\run_workout\Entity\RunWorkoutViewBuilder",
 *         "list_builder" = "Drupal\run_workout\Entity\RunWorkoutListBuilder",
 *         "form" = {
 *             "default" = "Drupal\run_workout\Form\RunWorkoutForm",
 *             "add" = "Drupal\run_workout\Form\RunWorkoutForm",
 *             "edit" = "Drupal\run_workout\Form\RunWorkoutForm",
 *             "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *             "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *         },
 *         "route_provider" = {
 *             "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *         },
 *     },
 *     base_table = "runworkout",
 *     admin_permission = "administer site configuration",
 *     entity_keys = {
 *         "id" = "id",
 *         "label" = "title",
 *         "uuid" = "uuid",
 *     },
 *     links = {
 *         "canonical" = "/admin/structure/runworkout/{runworkout}",
 *         "add-form" = "/admin/structure/runworkout/add",
 *         "edit-form" = "/admin/structure/runworkout/{runworkout}/edit",
 *         "delete-form" = "/admin/structure/runworkout/{runworkout}/delete",
 *         "collection" = "/admin/structure/runworkout",
 *         "delete-multiple-form" = "/admin/structure/runworkout/delete-multiple",
 *     },
 * )
 */
class RunWorkout extends ContentEntityBase implements RunWorkoutInterface
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
    public function setTitle($title)
    {
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

        $fields['description'] = BaseFieldDefinition::create('text_with_summary')
            ->setLabel(t('Description'))
            ->setDescription(t('Workout description (optional)'))
            ->setRequired(FALSE)
            ->setSettings([
                'display_summary' => FALSE,
                'required_summary' => FALSE,
            ])
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'text_default',
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayOptions('form', [
                'type' => 'text_textarea',
                'rows' => 5,
            ])
            ->setDisplayConfigurable('form', TRUE);

        $fields['workoutstep'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Steps'))
            ->setDescription(t('Steps in this workout'))
            ->setSettings(['target_type' => 'workoutstep'])
            ->setRequired(TRUE)
            ->setCardinality(-1)
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'entity_reference_label',
            ])
            ->setDisplayConfigurable('view', TRUE)
            ->setDisplayOptions('form', [
                'type' => 'entity_reference_label',
            ])
            ->setDisplayConfigurable('form', TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));

        return $fields;
    }
}
