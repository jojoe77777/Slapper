<?php

declare(strict_types=1);

namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use slapper\SlapperTrait;

class SlapperEntity extends Entity {
	use SlapperTrait;

	const TYPE_ID = 0;
	const HEIGHT = 0;

	public function __construct(Level $level, CompoundTag $nbt) {
		$this->height = static::HEIGHT;
		$this->width = $this->width ?? 1; //polyfill
		parent::__construct($level, $nbt);
		$this->prepareMetadata();
	}

    public function saveNBT() : void {
		parent::saveNBT();
		$this->saveSlapperNbt();
	}

	protected function sendSpawnPacket(Player $player) : void {
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = static::TYPE_ID;
		$pk->position = $this->asVector3();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->getDataPropertyManager()->getAll();
		$pk->metadata[self::DATA_NAMETAG] = [self::DATA_TYPE_STRING, $this->getDisplayName($player)];

		$player->dataPacket($pk);
	}
}
