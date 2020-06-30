<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\noise\bukkit\OctaveGenerator;
use muqsit\vanillagenerator\generator\noise\glowstone\SimplexOctaveGenerator;
use muqsit\vanillagenerator\generator\object\Flower;
use muqsit\vanillagenerator\generator\overworld\biome\BiomeIds;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class FlowerForestPopulator extends ForestPopulator{

	/** @var Block[] */
	protected static $FLOWERS;

	protected static function initFlowers() : void{
		self::$FLOWERS = [
			BlockFactory::get(BlockIds::POPPY),
			BlockFactory::get(BlockIds::POPPY),
			BlockFactory::get(BlockIds::DANDELION),
		];
	}

	/** @var OctaveGenerator */
	private $noiseGen;

	protected function initPopulators() : void{
		parent::initPopulators();
		$this->treeDecorator->setAmount(6);
		$this->flowerDecorator->setAmount(0);
		$this->doublePlantLoweringAmount = 1;
		$this->noiseGen = SimplexOctaveGenerator::fromRandomAndOctaves(new Random(2345), 1, 0, 0, 0);
		$this->noiseGen->setScale(1 / 48.0);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::MUTATED_FOREST];
	}

	public function populateOnGround(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		parent::populateOnGround($level, $chunkX, $chunkZ, $random);

		$sourceX = $chunk->getX() << 4;
		$sourceZ = $chunk->getZ() << 4;

		for($i = 0; $i < 100; ++$i){
			$x = $sourceX + $random->nextBoundedInt(16);
			$z = $sourceZ + $random->nextBoundedInt(16);
			$y = $random->nextBoundedInt($chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f) + 32);
			$noise = ($this->noiseGen->noise($x, $z, 0.5, 0, 2.0, false) + 1.0) / 2.0;
			$noise = $noise < 0 ? 0 : ($noise > 0.9999 ? 0.9999 : $noise);
			$flower = self::$FLOWERS[(int) ($noise * count(self::$FLOWERS))];
			(new Flower($flower))->generate($world, $random, $x, $y, $z);
		}
	}
}

FlowerForestPopulator::init();