<?php

declare(strict_types=1);

namespace slapper\events;

use pocketmine\event\entity\EntityEvent;
use pocketmine\Player;
use pocketmine\entity\Entity;

class SlapperCreationEvent extends EntityEvent {

    const CAUSE_COMMAND = 0;

    /** @var Entity */
    protected $entity;
    /** @var string */
    private $type;
    /** @var Player|null */
    private $creator;
    /** @var int */
    private $cause;


    /**
     * @param Entity      $entity
     * @param string      $type
     * @param Player|null $creator
     * @param int         $cause
     */
    public function __construct(Entity $entity, $type, Player $creator = null, $cause = self::CAUSE_COMMAND) {
        $this->entity = $entity;
        $this->type = $type;
        $this->creator = $creator;
        $this->cause = $cause;
    }

    public function getEntity(): Entity {
        return $this->entity;
    }

    public function getCreator(): ?Player {
        return $this->creator;
    }

    public function hasCreator(): bool {
        return $this->creator !== null;
    }

    public function getCause(): int {
        return $this->cause;
    }

    public function getType(): string {
        return $this->type;
    }

}
