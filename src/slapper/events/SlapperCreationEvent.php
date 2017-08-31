<?php

namespace slapper\events;

use pocketmine\event\entity\EntityEvent;
use pocketmine\Player;
use slapper\entities\SlapperEntity;

class SlapperCreationEvent extends EntityEvent {
	public static $handlerList = null;

	const CAUSE_COMMAND = 0;

	/** @var SlapperEntity */
	protected $entity;
	/** @var string */
	private $type;
	/** @var Player|null */
	private $creator;
	/** @var int */
	private $cause;


	/**
	 * @param SlapperEntity $entity
	 * @param string        $type
	 * @param Player|null   $creator
	 * @param int           $cause
	 */
	public function __construct(SlapperEntity $entity, $type, Player $creator = null, $cause = self::CAUSE_COMMAND) {
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
