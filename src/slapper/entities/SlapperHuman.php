<?php

declare(strict_types=1);

namespace slapper\entities;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use slapper\SlapperTrait;

class SlapperHuman extends Human {
    use SlapperTrait;

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->prepareMetadata();
    }

    public function saveNBT(): void {
        parent::saveNBT();
        $this->saveSlapperNbt();
    }

    protected function sendSpawnPacket(Player $player): void {
        parent::sendSpawnPacket($player);

        $this->sendData($player, [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName($player)]]);

        if (($menuName = $this->namedtag->getString("MenuName", "", true)) !== "") {
            $player->getServer()->updatePlayerListData($this->getUniqueId(), $this->getId(), $menuName, $this->skin, "", [$player]);
        }
    }
}
