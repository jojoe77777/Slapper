<?php

declare(strict_types=1);

namespace slapper;

use pocketmine\entity\DataPropertyManager;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\Player;

/**
 * Trait containing methods used in various Slappers.
 */
trait SlapperTrait {
    /** @var CompoundTag */
    public $namedtag;

    /**
     * @return DataPropertyManager
     */
    abstract public function getDataPropertyManager(): DataPropertyManager;

    /**
     * @return string
     */
    abstract public function getNameTag();

    abstract public function isNameTagVisible(): bool;

    abstract public function isNameTagAlwaysVisible(): bool;

    abstract public function setNameTagVisible(bool $value);

    abstract public function setNameTagAlwaysVisible(bool $value);

    abstract public function setGenericFlag(int $flag, bool $value = true);

    public function prepareMetadata(): void {
        if (!$this->namedtag->hasTag("NameVisibility", IntTag::class)) {
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
        $this->setGenericFlag(Entity::DATA_FLAG_IMMOBILE, true);
        if (!$this->namedtag->hasTag("Scale", FloatTag::class)) {
            $this->namedtag->setFloat("Scale", 1.0, true);
        }
        $this->getDataPropertyManager()->setFloat(Entity::DATA_SCALE, $this->namedtag->getFloat("Scale"));
    }

    public function tryChangeMovement(){

    }

    public function sendData($player, array $data = null) {
        if(!is_array($player)){
            $player = [$player];
        }

        foreach($player as $p){
            $playerData = $data ?? $this->getDataPropertyManager()->getAll();
            $playerData[self::DATA_NAMETAG] = [self::DATA_TYPE_STRING, $this->getDisplayName($p)];
            $pk = new SetEntityDataPacket();
            $pk->entityRuntimeId = $this->getId();
            $pk->metadata = $playerData;
            $p->dataPacket($pk);
        }
    }

    public function saveSlapperNbt(): void {
        $visibility = 0;
        if ($this->isNameTagVisible()) {
            $visibility = 1;
            if ($this->isNameTagAlwaysVisible()) {
                $visibility = 2;
            }
        }
        $scale = $this->getDataPropertyManager()->getFloat(Entity::DATA_SCALE);
        $this->namedtag->setInt("NameVisibility", $visibility, true);
        $this->namedtag->setFloat("Scale", $scale, true);
    }

    public function getDisplayName(Player $player): string {
        $vars = [
            "{name}" => $player->getName(),
            "{display_name}" => $player->getName(),
            "{nametag}" => $player->getNameTag()
        ];
        return str_replace(array_keys($vars), array_values($vars), $this->getNameTag());
    }
}
