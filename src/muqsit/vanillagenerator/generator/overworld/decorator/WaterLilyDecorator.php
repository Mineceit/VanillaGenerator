<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockFactory;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;

class WaterLilyDecorator extends Decorator{

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
		$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
		$sourceY = $random->nextBoundedInt($chunk->getHighestBlockAt($sourceX & 0x0f, $sourceZ & 0x0f) << 1);
		while($world->getBlockAt($sourceX, $sourceY - 1, $sourceZ) === BlockIds::AIR && $sourceY > 0){
			--$sourceY;
		}

		for($j = 0; $j < 10; ++$j){
			$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

			if(
				$y >= 0 && $y <= Level::Y_MAX && $world->getBlockAt($x, $y, $z) === BlockIds::AIR &&
				$world->getBlockIdAt($x, $y - 1, $z) === BlockIds::STILL_WATER
			){
				$world->setBlockIdAt($x, $y, $z, BlockIds::LILY_PAD);
			}
		}
	}
}