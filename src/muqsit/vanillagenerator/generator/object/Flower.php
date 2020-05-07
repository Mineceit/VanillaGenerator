<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\object;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class Flower extends TerrainObject{

	/** @var Block */
	private $block;

	public function __construct(Block $block){
		$this->block = $block;
	}

	public function generate(ChunkManager $world, Random $random, int $sourceX, int $sourceY, int $sourceZ) : bool{
		$succeeded = false;
		$height = $world->getWorldHeight();
		for($i = 0; $i < 64; ++$i){
			$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

			$block = $world->getBlockIdAt($x, $y, $z);
			if($y < $height && $block === BlockIds::AIR && $world->getBlockAt($x, $y - 1, $z) === BlockIds::GRASS){
				$world->setBlockIdAt($x, $y, $z, $this->block->getId());
				$succeeded = true;
			}
		}

		return $succeeded;
	}
}