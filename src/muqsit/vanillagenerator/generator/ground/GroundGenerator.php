<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\ground;

use muqsit\vanillagenerator\generator\overworld\biome\BiomeClimateManager;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class GroundGenerator{

	/** @var int */
	private $topMaterial;

	/** @var int */
	private $groundMaterial;

	public function __construct(){
		$this->setTopMaterial(BlockIds::GRASS);
		$this->setGroundMaterial(BlockIds::DIRT);
	}

	final protected function setTopMaterial(int $topMaterial) : void{
		$this->topMaterial = $topMaterial;
	}

	final protected function setGroundMaterial(int $groundMaterial) : void{
		$this->groundMaterial = $groundMaterial;
	}

	/**
	 * Generates a terrain column.
	 *
	 * @param ChunkManager $world the affected world
	 * @param Random $random the PRNG to use
	 * @param int $x the chunk X coordinate
	 * @param int $z the chunk Z coordinate
	 * @param int $biome the biome this column is in
	 * @param float $surfaceNoise the amplitude of random variation in surface height
	 */
	public function generateTerrainColumn(ChunkManager $world, Random $random, int $x, int $z, int $biome, float $surfaceNoise) : void{
		$seaLevel = 64;

		$topMat = $this->topMaterial;
		$groundMat = $this->groundMaterial;

		$chunkX = $x;
		$chunkZ = $z;

		$surfaceHeight = max((int) ($surfaceNoise / 3.0 + 3.0 + $random->nextFloat() * 0.25), 1);
		$deep = -1;

		$air = BlockIds::AIR;
		$stone = BlockIds::STONE;
		$sandstone = BlockIds::SANDSTONE;
		$gravel = BlockIds::GRAVEL;

		for($y = 255; $y >= 0; --$y){
			if($y <= $random->nextBoundedInt(5)){
				$world->setBlockIdAt($x, $y, $z, BlockIds::BEDROCK);
			}else{
				$matId = $world->getBlockIdAt($x, $y, $z);
				if($matId === BlockIds::AIR){
					$deep = -1;
				}elseif($matId === BlockIds::STONE){
					if($deep === -1){
						if($y >= $seaLevel - 5 && $y <= $seaLevel){
							$topMat = $this->topMaterial;
							$groundMat = $this->groundMaterial;
						}

						$deep = $surfaceHeight;
						if($y >= $seaLevel - 2){
							$world->setBlockIdAt($x, $y, $z, $topMat);
						}elseif($y < $seaLevel - 8 - $surfaceHeight){
							$topMat = $air;
							$groundMat = $stone;
							$world->setBlockIdAt($x, $y, $z, $gravel);
						}else{
							$world->setBlockIdAt($x, $y, $z, $groundMat);
						}
					}elseif($deep > 0){
						--$deep;
						$world->setBlockIdAt($x, $y, $z, $groundMat);

						if($deep === 0 && $groundMat === BlockIds::SAND){
							$deep = $random->nextBoundedInt(4) + max(0, $y - $seaLevel - 1);
							$groundMat = $sandstone;
						}
					}
				}elseif($matId === BlockIds::STILL_WATER && $y === $seaLevel - 2 && BiomeClimateManager::isCold($biome, $chunkX, $y, $chunkZ)){
					$world->setBlockIdAt($x, $y, $z, BlockIds::ICE);
				}
			}
		}
	}
}