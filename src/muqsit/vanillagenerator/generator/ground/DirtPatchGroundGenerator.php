<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\ground;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class DirtPatchGroundGenerator extends GroundGenerator{

	public function generateTerrainColumn(ChunkManager $world, Random $random, int $x, int $z, int $biome, float $surfaceNoise) : void{
		if($surfaceNoise > 1.75){
			$this->setTopMaterial(Block::DIRT);
		}elseif($surfaceNoise > -0.95){
			$this->setTopMaterial(Block::PODZOL);
		}else{
			$this->setTopMaterial(Block::GRASS);
		}
		$this->setGroundMaterial(Block::DIRT);

		parent::generateTerrainColumn($world, $random, $x, $z, $biome, $surfaceNoise);
	}
}