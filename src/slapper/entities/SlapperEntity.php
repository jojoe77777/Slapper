<?php
namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
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

	public function prepareMetadata() {
		if(!$this->namedtag->hasTag("NameVisibility", IntTag::class)) {
			$this->namedtag->setInt("NameVisibility", 2, true);
		}
		switch ($this->namedtag->getInt("NameVisibility")) {
			case 0:
				$this->setNameTagVisible(false);
				$this->setNameTagAlwaysVisible(false);
				break;
			case 1:
				$this->setNameTagVisible(true);
				$this->setNameTagAlwaysVisible(false);
				break;
			case 2:
				$this->setNameTagVisible(true);
				$this->setNameTagAlwaysVisible(true);
				break;
			default:
				$this->setNameTagVisible(true);
				$this->setNameTagAlwaysVisible(true);
				break;
		}
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_IMMOBILE, true);
		if(!$this->namedtag->hasTag("Scale", FloatTag::class)) {
			$this->namedtag->setFloat("Scale", 1.0, true);
		}
		$this->getDataPropertyManager()->setFloat(self::DATA_SCALE, $this->namedtag->getFloat("Scale"));
		$this->getDataPropertyManager()->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, static::HEIGHT);
	}

	public function saveNBT() : void{
		parent::saveNBT();
		$this->saveSlapperNbt();
	}

	protected function sendSpawnPacket(Player $player) : void{
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
