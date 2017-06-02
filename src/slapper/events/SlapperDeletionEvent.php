<?php

namespace slapper\events;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityEvent;

class SlapperDeletionEvent extends EntityEvent {

	public static $handlerList = null;

	public function __construct(Entity $entity) {
		$this->entity = $entity;
	}

}
