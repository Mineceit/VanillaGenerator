<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\ground;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class StonePatchGroundGenerator extends GroundGenerator{

	public function generateTerrainColumn(ChunkManager $world, Random $random, int $x, int $z, int $biome, float $surfaceNoise) : void{
		if($surfaceNoise > 1.0){
			$this->setTopMaterial(VanillaBlocks::STONE()->getId());
			$this->setGroundMaterial(VanillaBlocks::STONE()->getId());
		}else{
			$this->setTopMaterial(VanillaBlocks::GRASS()->getId());
			$this->setGroundMaterial(VanillaBlocks::DIRT()->getId());
		}

		parent::generateTerrainColumn($world, $random, $x, $z, $biome, $surfaceNoise);
	}
}