<?php
namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

class SlapperEntity extends Entity {

	const TYPE_ID = 0;
	const HEIGHT = 0;

	public function __construct(Level $level, CompoundTag $nbt) {
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
		$this->getDataPropertyManager()->setPropertyValue(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $this->namedtag->getFloat("Scale"));
		$this->getDataPropertyManager()->setPropertyValue(self::DATA_BOUNDING_BOX_HEIGHT, self::DATA_TYPE_FLOAT, static::HEIGHT);
	}

	public function saveNBT() {
		parent::saveNBT();
		$visibility = 0;
		if($this->isNameTagVisible()) {
			$visibility = 1;
			if($this->isNameTagAlwaysVisible()) {
				$visibility = 2;
			}
		}
		$scale = $this->getDataPropertyManager()->getFloat(Entity::DATA_SCALE);
		$this->namedtag->NameVisibility = new IntTag("NameVisibility", $visibility);
		$this->namedtag->Scale = new FloatTag("Scale", $scale);
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

	public function getDisplayName(Player $player) {
		$vars = [
			"{name}" => $player->getName(),
			"{display_name}" => $player->getName(),
			"{nametag}" => $player->getNameTag()
		];
		return str_replace(array_keys($vars), array_values($vars), $this->getNameTag());
	}

}
