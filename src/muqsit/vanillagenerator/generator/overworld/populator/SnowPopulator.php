<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator;

use muqsit\vanillagenerator\generator\overworld\biome\BiomeClimateManager;
use muqsit\vanillagenerator\generator\Populator;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class SnowPopulator extends Populator
{

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$sourceX = $chunkX << 4;
		$sourceZ = $chunkZ << 4;

		if($level->getChunk($chunkX, $chunkZ) == null) {
			return;
		}

		for($x = $sourceX; $x < $sourceX + 16; ++$x){
			for($z = $sourceZ; $z < $sourceZ + 16; ++$z){
				$y = $level->getChunk($chunkX, $chunkZ)->getHighestBlockAt($x & 0x0f, $z & 0x0f) - 1;
				if(BiomeClimateManager::isSnowy($level->getChunk($chunkX, $chunkZ)->getBiomeId($x & 0x0f, $z & 0x0f), $x, $y, $z)){
					$block = $level->getBlockIdAt($x, $y, $z);
					$blockAbove = $level->getBlockIdAt($x, $y + 1, $z);
					switch($block){
						case BlockIds::WATER:
						case BlockIds::STILL_WATER:
						case BlockIds::SNOW:
						case BlockIds::ICE:
						case BlockIds::PACKED_ICE:
						case BlockIds::YELLOW_FLOWER:
						case BlockIds::RED_FLOWER:
						case BlockIds::TALL_GRASS:
						case BlockIds::DOUBLE_PLANT:
						case BlockIds::SUGARCANE_BLOCK:
						case BlockIds::LAVA:
						case BlockIds::STILL_LAVA:
							break;
						case BlockIds::DIRT:
							$level->setBlockIdAt($x, $y, $z, BlockIds::GRASS);
							if($blockAbove === BlockIds::AIR){
								$level->setBlockIdAt($x, $y + 1, $z, BlockIds::SNOW_LAYER);
							}
							break;
						default:
							if($blockAbove === BlockIds::AIR){
								$level->setBlockIdAt($x, $y + 1, $z, BlockIds::SNOW_LAYER);
							}
							break;
					}
				}
			}
		}
	}
}