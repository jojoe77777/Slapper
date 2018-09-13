<?php

declare(strict_types=1);

namespace slapper;

use pocketmine\entity\DataPropertyManager;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\Player;

/**
 * Trait containing methods used in various Slappers.
 */
trait SlapperTrait {

	/** @var string[] */
    protected $commands = [];

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
        $this->setImmobile(true);

        if($nbt->hasTag("Commands", CompoundTag::class)){
        	foreach($nbt->getCompoundTag("Commands")->getValue() as $tag){
        		if($tag instanceof StringTag){
        			$this->addCommand($tag->getValue());
		        }
	        }
        }

        if (!$nbt->hasTag("Scale", FloatTag::class)) {
            $nbt->setFloat("Scale", 1.0, true);
        }

        $this->setScale($nbt->getFloat("Scale"));
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

        $cmdNbt = new CompoundTag("Commands");

        foreach($this->commands as $cmd){
        	$cmdNbt->setString($cmd, $cmd);
        }

        $nbt->setTag($cmdNbt);
    }

    public function getDisplayName(Player $player): string {
        $vars = [
            "{name}" => $player->getName(),
            "{display_name}" => $player->getName(),
            "{nametag}" => $player->getNameTag()
        ];
        return str_replace(array_keys($vars), array_values($vars), $this->getNameTag());
    }

	/**
	 * @param string $cmd
	 */
	public function addCommand(string $cmd) : void{
    	$this->commands[$cmd] = $cmd;
    }

	/**
	 * @param string $cmd
	 */
	public function removeCommand(string $cmd) : void{
    	unset($this->commands[$cmd]);
    }

	/**
	 * @param string $cmd
	 *
	 * @return bool
	 */
	public function hasCommand(string $cmd) : bool{
		return isset($this->commands[$cmd]);
    }

	/**
	 * @return string[]
	 */
	public function getCommands() : array{
		return $this->commands;
	}
}
