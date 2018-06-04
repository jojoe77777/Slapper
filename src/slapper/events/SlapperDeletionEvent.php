<?php

declare(strict_types=1);

namespace slapper\events;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityEvent;

class SlapperDeletionEvent extends EntityEvent {

    public function __construct(Entity $entity) {
        $this->entity = $entity;
    }

}
