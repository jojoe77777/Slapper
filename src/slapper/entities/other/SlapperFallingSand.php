<?php
namespace slapper\entities\other;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use slapper\entities\SlapperEntity;

class SlapperFallingSand extends SlapperEntity {

    public $entityId = 66;
    public $offset = 0.5;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        if(!isset($this->namedtag->BlockID)){
            $this->namedtag->BlockID = new IntTag("BlockID", 1);
        }
        $this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $this->namedtag->BlockID->getValue());
    }

    public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->BlockID = new IntTag("BlockID", $this->getDataProperty(self::DATA_VARIANT));
    }

}