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

	public $offset = 0;
	public $entityId = 0;

	public $offsets = [
		10 => 0.4, 11 => 0.8, 12 => 0.6, 13 => 0.8,
		14 => 0.4, 15 => 1.4, 16 => 0.8, 17 => 0.6, 18 => 0.4,
		19 => 0.4, 20 => 2.4, 21 => 1.2, 22 => 0.4, 23 => 1.2,
		24 => 1.2, 25 => 1.2, 26 => 1.2, 27 => 1.2, 32 => 1.4,
		33 => 1.4, 34 => 1.4, 35 => 0.5, 36 => 1.4, 37 => 1.0,
		38 => 2.4, 39 => 0.4, 40 => 0.2, 41 => 4.5, 42 => 1.0,
		43 => 1.4, 44 => 1.4, 45 => 1.6, 46 => 1.4, 47 => 1.4,
		48 => 2.1, 65 => 1.0, 66 => 0.5, 84 => 0.5, 90 => 0.5
	];

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		if(!isset($this->namedtag->NameVisibility)) {
			$this->namedtag->NameVisibility = new IntTag("NameVisibility", 2);
		}
		switch ($this->namedtag->NameVisibility->getValue()) {
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
		if(!isset($this->namedtag->Scale)) {
			$this->namedtag->Scale = new FloatTag("Scale", 1.0);
		}
		$this->setDataProperty(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $this->namedtag->Scale->getValue());
		$this->setDataProperty(self::DATA_BOUNDING_BOX_HEIGHT, self::DATA_TYPE_FLOAT, $this->offsets[$this->entityId]);
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
		$scale = $this->getDataProperty(Entity::DATA_SCALE);
		$this->namedtag->NameVisibility = new IntTag("NameVisibility", $visibility);
		$this->namedtag->Scale = new FloatTag("Scale", $scale);
	}

	public function spawnTo(Player $player) {
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->eid = $this->getId(); // TODO: remove when ALPHA6 is merged into master
		$pk->type = $this->entityId;
		$pk->x = $this->x;
		$pk->y = $this->y + $this->offset;
		$pk->z = $this->z;
		$pk->speedX = $pk->speedY = $pk->speedZ = 0.0;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$pk->metadata[self::DATA_NAMETAG] = [self::DATA_TYPE_STRING, $this->getDisplayName($player)];
		$player->dataPacket($pk);
		parent::spawnTo($player);
	}

	public function getDisplayName(Player $player) {
		return str_ireplace(["{name}", "{display_name}", "{nametag}"], [$player->getName(), $player->getDisplayName(), $player->getNametag()], $player->hasPermission("slapper.seeId") ? $this->getNameTag() . "\n" . \pocketmine\utils\TextFormat::GREEN . "Entity ID: " . $this->getId() : $this->getNameTag());
	}

}
