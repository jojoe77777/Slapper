<?php
namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class SlapperHuman extends Human {

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		if(!isset($this->namedtag->NameVisibility)) {
			$this->namedtag->setTag(new IntTag("NameVisibility", 2));
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
		if(!isset($this->namedtag->Scale)) {
			$this->namedtag->setTag(new FloatTag("Scale", 1.0));
		}
		$this->setDataProperty(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $this->namedtag->Scale->getValue());
	}

	// TODO: This can be removed when PMMP updates Human class to handle Capes and Custom Geometry
	protected function initHumanData(){
		parent::initHumanData();

		$skin = $this->namedtag->getCompoundTag("Skin");
		if($skin !== null){
			$name = $skin->getString("Name");
			$data = $skin->getString("Data");
			$cape = isset($skin["Cape"]) ? $skin->getString("Cape") : "";
			$geometryName = isset($skin["GeometryName"]) ? $skin->getString("GeometryName") : "";
			$geometryData = isset($skin["GeometryData"]) ? $skin->getByteArray("GeometryData") : "";
			$this->setSkin(new Skin($name, $data, $cape, $geometryName, $geometryData));
		}
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
		$this->namedtag->setTag(new IntTag("NameVisibility", $visibility));
		$this->namedtag->setTag(new FloatTag("Scale", $scale));
		if($this->skin !== null){
			// TODO: This will need to be updated when PMMP updates Human class to handle Capes and Custom Geometry
			$this->namedtag->setTag(new CompoundTag("Skin", [
				new StringTag("Data", $this->skin->getSkinData()),
				new StringTag("Name", $this->skin->getSkinId()),
				new StringTag("Cape", $this->skin->getCapeData()),
				new StringTag("GeometryName", $this->skin->getGeometryName()),
				new ByteArrayTag("GeometryData", $this->skin->getGeometryData())
			]) );
		}
	}

	protected function sendSpawnPacket(Player $player) : void{
		parent::sendSpawnPacket($player);

		$this->sendData($player, [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName($player)]]);

		if(isset($this->namedtag["MenuName"]) and $this->namedtag["MenuName"] !== "") {
			$player->getServer()->updatePlayerListData($this->getUniqueId(), $this->getId(), $this->namedtag["MenuName"], $this->skin, [$player]);
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
