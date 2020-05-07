<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\object;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class TallGrass extends TerrainObject{

	/** @var Block */
	private $grassType;

	public function __construct(Block $grassType){
		$this->grassType = $grassType;
	}

	public function generate(ChunkManager $world, Random $random, int $sourceX, int $sourceY, int $sourceZ) : bool{
		do{
			$thisBlockId = $world->getBlockAt($sourceX, $sourceY, $sourceZ);
			--$sourceY;
		}while(($thisBlockId === BlockLegacyIds::AIR || $thisBlockId === BlockLegacyIds::LEAVES) && $sourceY > 0);
		++$sourceY;
		$succeeded = false;
		$height = $world->getWorldHeight();
		for($i = 0; $i < 128; ++$i){
			$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
			$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

			$blockType = $world->getBlockAt($x, $y, $z);
			$blockTypeBelow = $world->getBlockAt($x, $y - 1, $z);
			if($y < $height && $blockType === BlockLegacyIds::AIR && ($blockTypeBelow === BlockLegacyIds::GRASS || $blockTypeBelow === BlockLegacyIds::DIRT)){
				$world->setBlockIdAt($x, $y, $z, $this->grassType->getId());
				$succeeded = true;
			}
		}
		return $succeeded;
	}
}