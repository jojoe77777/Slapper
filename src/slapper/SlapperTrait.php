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
    public $additionalNbt;

    /**
     * @return DataPropertyManager
     */
    abstract public function getDataPropertyManager(): DataPropertyManager;

    /**
     * @return string
     */
    abstract public function getNameTag(): string;

    abstract public function sendNameTag(Player $player): void;

    abstract public function setGenericFlag(int $flag, bool $value = true): void;

    public function prepareMetadata(CompoundTag $nbt): void {
    	if($nbt->hasTag("additionalNbt", CompoundTag::class)){
    		$this->additionalNbt = $nbt->getCompoundTag("additionalNbt");
	    }else{
    		$this->additionalNbt = new CompoundTag("additionalNbt");
	    }
        $this->setGenericFlag(Entity::DATA_FLAG_IMMOBILE, true);
        if (!$nbt->hasTag("Scale", FloatTag::class)) {
            $nbt->setFloat("Scale", 1.0, true);
        }
        $this->getDataPropertyManager()->setFloat(Entity::DATA_SCALE, $nbt->getFloat("Scale"));
    }

    public function tryChangeMovement(): void {

    }

    public function sendData($playerList, array $data = null): void {
        if(!is_array($playerList)){
            $playerList = [$playerList];
        }

        /** @var Player $p */
	    foreach($playerList as $p){
            $playerData = $data ?? $this->getDataPropertyManager()->getAll();
            unset($playerData[self::DATA_NAMETAG]);
            $pk = new SetEntityDataPacket();
            $pk->entityRuntimeId = $this->getId();
            $pk->metadata = $playerData;
            $p->sendDataPacket($pk);

            $this->sendNameTag($p);
        }
    }

    public function saveSlapperNbt(CompoundTag $nbt): void {
        $visibility = 0;
        if ($this->isNameTagVisible()) {
            $visibility = 1;
            if ($this->isNameTagAlwaysVisible()) {
                $visibility = 2;
            }
        }
        $scale = $this->getDataPropertyManager()->getFloat(Entity::DATA_SCALE);
        $nbt->setInt("NameVisibility", $visibility, true);
        $nbt->setFloat("Scale", $scale, true);
        $nbt->setTag($this->additionalNbt, true);
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
