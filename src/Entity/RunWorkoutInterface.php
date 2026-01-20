<?php

namespace Drupal\run_workout\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Represents a RunWorkout entity.
 */
interface RunWorkoutInterface extends ContentEntityInterface, EntityChangedInterface {
    /**
     * Gets the  name.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the  Name.
     *
     * @param string $name
     *
     * @return \Drupal\run_workout\Entity\RunWorkoutInterface
     *  The called  entity.
     */
    public function setTitle($name);

       /**
     * Gets the  creation timestamp.
     *
     * @return int
     */
    public function getCreatedTime();

    /**
     * Sets the  creation timestamp.
     *
     * @param int $timestamp
     *
     * @return \Drupal\run_workout\Entity\RunWorkoutInterface
     *   The called  entity.
     */
    public function setCreatedTime($timestamp);

}
