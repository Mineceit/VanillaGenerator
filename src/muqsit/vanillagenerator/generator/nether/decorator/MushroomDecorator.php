<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\nether\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class MushroomDecorator extends Decorator{

	private const MATERIALS = [BlockLegacyIds::NETHERRACK, BlockLegacyIds::QUARTZ_ORE, BlockLegacyIds::SOUL_SAND, BlockLegacyIds::GRAVEL];

	/** @var Block */
	private $type;

	public function __construct(Block $type){
		$this->type = $type;
	}

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$height = $world->getWorldHeight();

		$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
		$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
		$sourceY = $random->nextBoundedInt($height);

		for($i = 0; $i < 64; ++$i){
			$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

			$block = $world->getBlockIdAt($x, $y, $z);
			$blockBelow = $world->getBlockIdAt($x, $y - 1, $z);
			if(
				$y < $height &&
				$block === BlockLegacyIds::AIR &&
				in_array($blockBelow, self::MATERIALS, true)
			){
				$world->setBlockIdAt($x, $y, $z, $this->type->getId());
			}
		}
	}
}