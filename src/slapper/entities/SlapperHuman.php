<?php
namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

class SlapperHuman extends Human {

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
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
	}

	public function saveNBT() : void{
		parent::saveNBT();
		$visibility = 0;
		if($this->isNameTagVisible()) {
			$visibility = 1;
			if($this->isNameTagAlwaysVisible()) {
				$visibility = 2;
			}
		}
		$scale = $this->getDataPropertyManager()->getFloat(Entity::DATA_SCALE);
		$this->namedtag->setInt("NameVisibility", $visibility, true);
		$this->namedtag->setFloat("Scale", $scale, true);
	}

	protected function sendSpawnPacket(Player $player) : void{
		parent::sendSpawnPacket($player);

		$this->sendData($player, [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName($player)]]);

		if(($menuName = $this->namedtag->getString("MenuName", "", true)) !== "") {
			$player->getServer()->updatePlayerListData($this->getUniqueId(), $this->getId(), $menuName, $this->skin, "", [$player]);
		}

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
