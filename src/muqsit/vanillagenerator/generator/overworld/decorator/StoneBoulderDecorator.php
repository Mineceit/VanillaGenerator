<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use muqsit\vanillagenerator\generator\object\StoneBoulder;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class StoneBoulderDecorator extends Decorator{
	
	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$sourceX = $chunkX << 4;
        $sourceZ = $chunkZ << 4;
        for ($i = 0; $i < $random->nextBoundedInt(3); ++$i) {
			$x = $sourceX + $random->nextBoundedInt(16);
            $z = $sourceZ + $random->nextBoundedInt(16);
            $y = $level->getChunk($chunkX, $chunkZ)->getHighestBlockAt($x & 0x0f, $z & 0x0f);
			(new StoneBoulder())->generate($level, $random, $x, $y, $z);
        }
	}

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
	}
}