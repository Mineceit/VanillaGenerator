<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\object;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class SugarCane extends TerrainObject{

	private const FACES = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST];

	public function generate(ChunkManager $world, Random $random, int $x, int $y, int $z) : bool{
		if($world->getBlockIdAt($x, $y, $z) !== BlockLegacyIds::AIR){
			return false;
		}

		$vec = new Vector3($x, $y - 1, $z);
		$adjacentWater = false;
		foreach(self::FACES as $face){
			// needs a directly adjacent water block
			$blockTypeV = $vec->getSide($face);
			$blockType = $world->getBlockIdAt($blockTypeV->x, $blockTypeV->y, $blockTypeV->z);
			if($blockType === BlockLegacyIds::STILL_WATER || $blockType === BlockLegacyIds::WATER){
				$adjacentWater = true;
				break;
			}
		}
		if(!$adjacentWater){
			return false;
		}
		for($n = 0; $n <= $random->nextBoundedInt($random->nextBoundedInt(3) + 1) + 1; ++$n){
			$block = $world->getBlockIdAt($x, $y + $n - 1, $z);
			$blockId = $block;
			if($blockId === BlocKLegacyIds::SUGARCANE_BLOCK
				|| $blockId === BlocKLegacyIds::GRASS
				|| $blockId === BlocKLegacyIds::SAND
				|| ($blockId === BlocKLegacyIds::DIRT)
			){
				$caneBlock = $world->getBlockIdAt($x, $y + $n, $z);
				if($caneBlock !== BlockLegacyIds::AIR && $world->getBlockIdAt($x, $y + $n + 1, $z) !== BlockLegacyIds::AIR){
					return $n > 0;
				}

				$world->setBlockIdAt($x, $y + $n, $z, VanillaBlocks::SUGARCANE()->getId());
			}
		}
		return true;
	}
}