<?php

namespace slapper\events;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\entity\EntityEvent;
use pocketmine\Player;

class SlapperHitEvent extends EntityEvent implements Cancellable {

	public static $handlerList = null;

	/** @var Player */
	private $damager;

	public function __construct(Entity $entity, Player $damager) {
		$this->entity = $entity;
		$this->damager = $damager;
	}

	/**
	 * @return Player
	 */
	public function getDamager() : Player {
		return $this->damager;
	}
}
