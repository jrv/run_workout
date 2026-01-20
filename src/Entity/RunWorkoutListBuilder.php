<?php

namespace Drupal\run_workout\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * RunWorkoutListBuilderInterface implementation responsible for the RunWorkout entities.
 */
class RUnWorkoutListBuilder extends EntityListBuilder {

    /**
     * {@inheritdoc}
     */
    public function buildHeader() {
        $header['title'] = $this->t('Titel');
        return $header + parent::buildHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function buildRow(EntityInterface $entity) {
        /* @var $entity \Drupal\run_workout\Entity\RunWorkout */
        $row['title'] = $entity->toLink();
        return $row + parent::buildRow($entity);
    }

}
