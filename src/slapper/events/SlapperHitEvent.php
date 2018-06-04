<?php

declare(strict_types=1);

namespace slapper\events;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\entity\EntityEvent;
use pocketmine\Player;

class SlapperHitEvent extends EntityEvent implements Cancellable {

    /** @var Player */
    private $damager;

    public function __construct(Entity $entity, Player $damager) {
        $this->entity = $entity;
        $this->damager = $damager;
    }

    /**
     * @return Player
     */
    public function getDamager(): Player {
        return $this->damager;
    }
}
