<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\ground;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class DirtAndStonePatchGroundGenerator extends GroundGenerator{

	public function generateTerrainColumn(ChunkManager $world, Random $random, int $x, int $z, int $biome, float $surfaceNoise) : void{
		if($surfaceNoise > 1.75){
			$this->setTopMaterial(BlockFactory::get(Block::STONE)->getId());
			$this->setGroundMaterial(BlockFactory::get(Block::STONE)->getId());
		}elseif($surfaceNoise > -0.5){
			$this->setTopMaterial(BlockFactory::get(Block::DIRT)->getId());
			$this->setGroundMaterial(VanillaBlocks::DIRT()->getId());
		}else{
			$this->setTopMaterial(VanillaBlocks::GRASS()->getId());
			$this->setGroundMaterial(VanillaBlocks::DIRT()->getId());
		}

		parent::generateTerrainColumn($world, $random, $x, $z, $biome, $surfaceNoise);
	}
}