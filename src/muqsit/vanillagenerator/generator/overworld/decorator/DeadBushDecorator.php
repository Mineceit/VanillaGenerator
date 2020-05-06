<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockFactory;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class DeadBushDecorator extends Decorator{

	private const SOIL_TYPES = [BlockIds::SAND, BlockIds::DIRT, BlockIds::HARDENED_CLAY, BlockIds::STAINED_CLAY];

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
		$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
		$sourceY = $random->nextBoundedInt($chunk->getHighestBlockAt($sourceX & 0x0f, $sourceZ & 0x0f) << 1);
		while($sourceY > 0
			&& ($world->getBlockIdAt($sourceX, $sourceY, $sourceZ) === BlockIds::AIR
				|| $world->getBlockIdAt($sourceX, $sourceY, $sourceZ) === BlockIds::LEAVES)){
			--$sourceY;
		}

		for($i = 0; $i < 4; ++$i){
			$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

			if($world->getBlockIdAt($x, $y, $z) === BlockIds::AIR){
				$blockBelow = $world->getBlockIdAt($x, $y - 1, $z);
				foreach(self::SOIL_TYPES as $soil){
					if($soil === $blockBelow){
						$world->setBlockIdAt($x, $y, $z, BlockIds::DEAD_BUSH);
						break;
					}
				}
			}
		}
	}
}