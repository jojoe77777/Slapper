<?php

declare(strict_types=1);

namespace slapper\entities;

class SlapperElderGuardian extends SlapperEntity {

    const TYPE_ID = 50;
    const HEIGHT = 1.9975;

    public function prepareMetadata(): void {
        $this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ELDER, true);
        parent::prepareMetadata();
    }

}
