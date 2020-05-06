<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class PumpkinDecorator extends Decorator{

	private const FACES = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST];

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		if($random->nextBoundedInt(32) === 0){
			$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
			$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
			$sourceY = $random->nextBoundedInt($chunk->getHighestBlockAt($sourceX & 0x0f, $sourceZ & 0x0f) << 1);

			$block_factory = new BlockFactory();

			for($i = 0; $i < 64; ++$i){
				$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
				$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
				$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

				if($world->getBlockAt($x, $y, $z) === BlockIds::AIR && $world->getBlockAt($x, $y - 1, $z) === BlockIds::GRASS){
					$world->setBlockIdAt($x, $y, $z, BlockIds::PUMPKIN);
				}
			}
		}
	}
}