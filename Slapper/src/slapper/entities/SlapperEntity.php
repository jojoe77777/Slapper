<?php
namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class SlapperEntity extends Entity {

    public $offset = 0;
    public $entityId = 0;

    public $offsets = [
        10 => 0.4,
        11 => 0.8,
        12 => 0.6,
        13 => 0.8,
        14 => 0.4,
        15 => 1.4,
        16 => 0.8,
        17 => 0.6,
        18 => 0.4,
        19 => 0.4,
        20 => 2.4,
        21 => 1.2,
        22 => 0.4,
        23 => 1.2,
        24 => 1.2,
        25 => 1.2,
        26 => 1.2,
        27 => 1.2,
        32 => 1.4,
        33 => 1.4,
        34 => 1.4,
        35 => 0.5,
        36 => 1.4,
        37 => 1.0,
        38 => 2.4,
        39 => 0.4,
        40 => 0.2,
        41 => 4.5,
        42 => 1.0,
        43 => 1.4,
        44 => 1.4,
        45 => 1.6,
        46 => 1.4,
        47 => 1.4,
        48 => 2.1,
        65 => 1.0,
        66 => 0.5,
        84 => 0.5,
        90 => 0.5
    ];

    public function getName() {
        return $this->getNameTag();
    }

    public function spawnTo(Player $player) {
        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = $this->entityId;
        $pk->x = $this->x;
        $pk->y = $this->y + $this->offset;
        $pk->z = $this->z;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        /* TODO: Fix bugs and remove this */
        $this->setNameTagVisible(true);
        $this->setNameTagAlwaysVisible(true);
        $pk->metadata = [
            self::DATA_FLAGS => [self::DATA_TYPE_LONG, ((1 << self::DATA_FLAG_NO_AI) | ($this->isNameTagVisible() ? (1 << self::DATA_FLAG_CAN_SHOW_NAMETAG) : 0))],
            self::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $this->getDisplayName($player)],
            self::DATA_LEAD_HOLDER_EID => [self::DATA_TYPE_LONG, -1],
            self::DATA_BOUNDING_BOX_HEIGHT => [self::DATA_TYPE_FLOAT, $this->offsets[$this->entityId]]
        ];
        $player->dataPacket($pk);
        parent::spawnTo($player);
    }

    public function getDisplayName(Player $player){
        return str_ireplace(["{name}", "{display_name}", "{nametag}"], [$player->getName(), $player->getDisplayName(), $player->getNametag()], $player->hasPermission("slapper.seeId") ? $this->getNameTag() . "\n" . \pocketmine\utils\TextFormat::GREEN . "Entity ID: " . $this->getId() : $this->getNameTag());
    }

}
