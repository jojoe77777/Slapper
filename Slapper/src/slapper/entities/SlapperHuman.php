<?php
namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\level\format\FullChunk;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\Player;

class SlapperHuman extends Human {

    public function __construct(FullChunk $chunk, CompoundTag $nbt){
        parent::__construct($chunk, $nbt);
        if(!isset($this->namedtag->NameVisibility)){
            $this->namedtag->NameVisibility = new IntTag("NameVisibility", 2);
        }
        switch($this->namedtag->NameVisibility->getValue()){
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
        if(!isset($this->namedtag->Scale)){
            $this->namedtag->Scale = new FloatTag("Scale", 1.0);
        }
        $this->setDataProperty(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $this->namedtag->Scale->getValue());
    }

    public function saveNBT(){
        parent::saveNBT();
        $visibility = 0;
        if($this->isNameTagVisible()){
            $visibility = 1;
            if($this->isNameTagAlwaysVisible()){
                $visibility = 2;
            }
        }
        $scale = $this->getDataProperty(Entity::DATA_SCALE);
        $this->namedtag->NameVisibility = new IntTag("NameVisibility", $visibility);
        $this->namedtag->Scale = new FloatTag("Scale", $scale);
    }

    public function spawnTo(Player $player) {
        if (!isset($this->hasSpawned[$player->getLoaderId()])) {
            $this->hasSpawned[$player->getLoaderId()] = $player;

            $uuid = $this->getUniqueId();
            $entityId = $this->getId();

            $pk = new AddPlayerPacket();
            $pk->uuid = $uuid;
            $pk->username = "";
            $pk->eid = $entityId;
            $pk->x = $this->x;
            $pk->y = $this->y;
            $pk->z = $this->z;
            $pk->yaw = $this->yaw;
            $pk->pitch = $this->pitch;
            $pk->item = $this->getInventory()->getItemInHand();
            $pk->metadata = $this->dataProperties;
            $pk->metadata[self::DATA_NAMETAG] = [self::DATA_TYPE_STRING, $this->getDisplayName($player)];
            $player->dataPacket($pk);
            $this->inventory->sendArmorContents($player);

            $add = new PlayerListPacket();
            $add->type = 0;
            $add->entries[] = [$uuid, $entityId, isset($this->namedtag->MenuName) ? $this->namedtag["MenuName"] : "", $this->skinId, $this->skin];
            $player->dataPacket($add);
            if ($this->namedtag["MenuName"] === "") {
                $remove = new PlayerListPacket();
                $remove->type = 1;
                $remove->entries[] = [$uuid];
                $player->dataPacket($remove);
            }
        }
    }

    public function getDisplayName(Player $player) {
        return str_ireplace(["{name}", "{display_name}", "{nametag}"], [$player->getName(), $player->getDisplayName(), $player->getNametag()], $player->hasPermission("slapper.seeId") ? $this->getNameTag() . "\n" . \pocketmine\utils\TextFormat::GREEN . "Entity ID: " . $this->getId() : $this->getNameTag());
    }
}
