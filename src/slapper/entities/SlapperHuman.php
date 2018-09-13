<?php

declare(strict_types=1);

namespace slapper\entities;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\Player;
use slapper\SlapperTrait;

class SlapperHuman extends Human{
	use SlapperTrait;

	/** @var string|null */
	protected $menuName;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->prepareMetadata($nbt);

		if($nbt->hasTag("MenuName", StringTag::class)){
			$this->menuName = $nbt->getString("MenuName");
		}
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$this->saveSlapperNbt($nbt);

		if($this->menuName !== null){
			$nbt->setString("MenuName", $this->menuName);
		}
		return $nbt;
	}

	public function sendNameTag(Player $player) : void{
		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->metadata = [
			self::DATA_NAMETAG => [
				self::DATA_TYPE_STRING,
				$this->getDisplayName($player)
			]
		];
		$player->sendDataPacket($pk);
	}

	/**
	 * @return null|string
	 */
	public function getMenuName() : ?string{
		return $this->menuName;
	}

	/**
	 * @param null|string $menuName
	 */
	public function setMenuName(?string $menuName) : void{
		$this->menuName = $menuName;
	}

	protected function sendSpawnPacket(Player $player) : void{
		parent::sendSpawnPacket($player);

		if($this->menuName !== null){
			$player->getServer()->updatePlayerListData($this->getUniqueId(), $this->getId(), $this->menuName, $this->skin, "", [$player]);
		}
	}
}
