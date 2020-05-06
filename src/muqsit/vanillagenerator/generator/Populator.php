<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator;

use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

interface Populator
{
	public function populate(ChunkManager $world, Random $random, Chunk $chunk) : void;
}
