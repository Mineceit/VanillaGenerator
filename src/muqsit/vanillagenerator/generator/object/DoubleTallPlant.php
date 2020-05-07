<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\object;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class DoubleTallPlant extends TerrainObject{

	/** @var Block */
	private $species;

	public function __construct(Block $species){
		$this->species = $species;
	}

	/**
	 * Generates up to 64 plants around the given point.
	 *
	 * @param ChunkManager $world
	 * @param Random $random
	 * @param int $sourceX
	 * @param int $sourceY
	 * @param int $sourceZ
	 * @return bool true whether least one plant was successfully generated
	 */
	public function generate(ChunkManager $world, Random $random, int $sourceX, int $sourceY, int $sourceZ) : bool{
		$placed = false;
		$height = $world->getWorldHeight();
		$block_factory = new BlockFactory();
		for($i = 0; $i < 64; ++$i){
			$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

			$block = $world->getBlockIdAt($x, $y, $z);
			$topBlock = $world->getBlockIdAt($x, $y + 1, $z);
			if($y < $height && $block === BlockIds::AIR && $topBlock === BlockIds::AIR && $world->getBlockIdAt($x, $y - 1, $z) === BlockIds::GRASS){
				$world->setBlockIdAt($x, $y, $z, $this->species->getId());
				$world->setBlockIdAt($x, $y + 1, $z, BlockIds::TALL_GRASS);
				$placed = true;
			}
		}

		return $placed;
	}
}