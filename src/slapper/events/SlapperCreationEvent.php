<?php

namespace slapper\events;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityEvent;
use pocketmine\Player;

class SlapperCreationEvent extends EntityEvent {

	const CAUSE_COMMAND = 0;

	protected $entity;
	/** @var string */
	private $type;
	/** @var Player | null */
	private $creator;
	private $cause;

	public static $handlerList = null;

	public function __construct(Entity $entity, $type, $creator = null, $cause = self::CAUSE_COMMAND) {
		$this->entity = $entity;
		$this->type = $type;
		$this->creator = $creator;
		$this->cause = $cause;
	}

	public function getEntity() {
		return $this->entity;
	}

	public function getCreator() {
		return $this->creator;
	}

	public function hasCreator() {
		return $this->creator !== null;
	}

	public function getCause() {
		return $this->cause;
	}

	public function getType() {
		return $this->type;
	}

}
