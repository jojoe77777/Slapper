<?php

declare(strict_types=1);

namespace slapper\entities;

use pocketmine\nbt\tag\CompoundTag;

class SlapperElderGuardian extends SlapperEntity {

    const TYPE_ID = 50;
    const HEIGHT = 1.9975;

    public function prepareMetadata(CompoundTag $nbt): void {
        $this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ELDER, true);
        parent::prepareMetadata($nbt);
    }

}
