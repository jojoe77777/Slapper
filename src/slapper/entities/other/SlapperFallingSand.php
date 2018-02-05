<?php
namespace slapper\entities\other;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use slapper\entities\SlapperEntity;

class SlapperFallingSand extends SlapperEntity {

	const TYPE_ID = 66;
	const HEIGHT = 0.98;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		if(!$this->namedtag->hasTag("BlockID", IntTag::class)){
			$this->namedtag->setInt("BlockID", 1, true);
		}
		$this->getDataPropertyManager()->setPropertyValue(self::DATA_VARIANT, self::DATA_TYPE_INT, $this->namedtag->getInt("BlockID"));
	}

	public function saveNBT() {
		parent::saveNBT();
		$this->namedtag->setInt("BlockID", $this->getDataPropertyManager()->getInt(self::DATA_VARIANT), true);
	}

}
