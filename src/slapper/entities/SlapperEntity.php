<?php

declare(strict_types=1);

namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket as AddEntityPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket as MoveEntityAbsolutePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket as RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket as SetEntityDataPacket;
use pocketmine\Player;
use pocketmine\utils\UUID;
use slapper\SlapperTrait;

class SlapperEntity extends Entity {
    use SlapperTrait;

    const TYPE_ID = 0;
    const HEIGHT = 0;

    /** @var int */
    private $tagId;

    public function __construct(Level $level, CompoundTag $nbt) {
        $this->height = static::HEIGHT;
        $this->width = $this->width ?? 1; //polyfill
        $this->tagId = Entity::$entityCount++;
        parent::__construct($level, $nbt);
        $this->prepareMetadata();
        $this->setNameTagVisible(false);
    }

    public function saveNBT(): void {
        parent::saveNBT();
        $this->saveSlapperNbt();
    }

    protected function sendSpawnPacket(Player $player): void {
        $pk = new AddEntityPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type = AddEntityPacket::LEGACY_ID_MAP_BC[static::TYPE_ID];
        $pk->position = $this->asVector3();
        $pk->yaw = $pk->headYaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->metadata = $this->getDataPropertyManager()->getAll();
        unset($pk->metadata[self::DATA_NAMETAG]);

        $player->dataPacket($pk);

        $pk2 = new AddPlayerPacket();
        $pk2->entityRuntimeId = $this->tagId;
        $pk2->uuid = UUID::fromRandom();
        $pk2->username = $this->getDisplayName($player);
        $pk2->position = $this->asVector3()->add(0, static::HEIGHT);
        $pk2->item = ItemFactory::get(ItemIds::AIR);
        $pk2->metadata = [self::DATA_SCALE => [self::DATA_TYPE_FLOAT, 0.0]];

        $player->dataPacket($pk2);
    }

    public function sendNameTag(Player $player): void {
        $pk = new SetEntityDataPacket();
        $pk->entityRuntimeId = $this->tagId;
        $pk->metadata = [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName($player)]];
        $player->dataPacket($pk);
    }

    public function despawnFrom(Player $player, bool $send = true): void {
        parent::despawnFrom($player, $send);
        $pk = new RemoveEntityPacket();
        $pk->entityUniqueId = $this->tagId;
        $player->dataPacket($pk);
    }

    public function broadcastMovement(bool $teleport = false): void {
        if($this->chunk !== null) {
            parent::broadcastMovement($teleport);
            $pk = new MoveEntityAbsolutePacket();
            $pk->entityRuntimeId = $this->tagId;
            $pk->position = $this->asVector3()->add(0, static::HEIGHT + 1.62);
            $pk->xRot = $pk->yRot = $pk->zRot = 0;

            $this->level->addChunkPacket($this->chunk->getX(), $this->chunk->getZ(), $pk);
        }
    }
}
