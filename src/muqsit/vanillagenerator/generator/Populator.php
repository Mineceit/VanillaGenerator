<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator;

use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

abstract class Populator extends \pocketmine\level\generator\populator\Populator
{
	abstract function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random);
}
