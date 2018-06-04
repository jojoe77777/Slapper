<?php

declare(strict_types=1);

namespace slapper\entities\other;

use pocketmine\block\BlockFactory;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use slapper\entities\SlapperEntity;

class SlapperFallingSand extends SlapperEntity {

    const TYPE_ID = 66;
    const HEIGHT = 0.98;

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        if (!$this->namedtag->hasTag("BlockID", IntTag::class)) {
            $this->namedtag->setInt("BlockID", 1, true);
        }

        //haxx: we shouldn't use toStaticRuntimeId() because it's internal, but there isn't really any better option at the moment
        $this->getDataPropertyManager()->setInt(self::DATA_VARIANT, BlockFactory::toStaticRuntimeId($this->namedtag->getInt("BlockID")));
    }

    public function saveNBT(): void {
        parent::saveNBT();
        $this->namedtag->setInt("BlockID", $this->getDataPropertyManager()->getInt(self::DATA_VARIANT), true);
    }

}
