<?php

declare(strict_types=1);

namespace slapper\entities;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\Player;
use slapper\SlapperTrait;

class SlapperHuman extends Human {
    use SlapperTrait;

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->prepareMetadata($nbt);
    }

    public function saveNBT(): CompoundTag {
        $nbt = parent::saveNBT();
        $this->saveSlapperNbt($nbt);
        return $nbt;
    }

    public function sendNameTag(Player $player): void {
        $pk = new SetEntityDataPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->metadata = [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName($player)]];
        $player->sendDataPacket($pk);
    }

    protected function sendSpawnPacket(Player $player): void {
        parent::sendSpawnPacket($player);

        if (($menuName = $this->saveNBT()->getString("MenuName", "", true)) !== "") {
            $player->getServer()->updatePlayerListData($this->getUniqueId(), $this->getId(), $menuName, $this->skin, "", [$player]);
        }
    }
}
