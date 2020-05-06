<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockFactory;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class MelonDecorator extends Decorator{

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
		$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
		$sea_level = 64;
		$sourceY = $random->nextBoundedInt($sea_level << 1);

		for($i = 0; $i < 64; ++$i){
			$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

			if(
				$world->getBlockIdAt($x, $y, $z) === BlockIds::AIR &&
				$world->getBlockIdAt($x, $y - 1, $z) === BlockIds::GRASS
			){
				$world->setBlockIdAt($x, $y, $z, BlockIds::MELON);
			}
		}
	}
}