<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\object\tree\BigOakTree;
use muqsit\vanillagenerator\generator\object\tree\CocoaTree;
use muqsit\vanillagenerator\generator\object\tree\JungleBush;
use muqsit\vanillagenerator\generator\object\tree\MegaJungleTree;
use muqsit\vanillagenerator\generator\overworld\biome\BiomeIds;
use muqsit\vanillagenerator\generator\overworld\decorator\MelonDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use pocketmine\utils\Random;
use pocketmine\block\BlockTransaction;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class JunglePopulator extends BiomePopulator {

	/** @var TreeDecoration[] */
	protected static $TREES;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(BigOakTree::class, 10),
			new TreeDecoration(JungleBush::class, 50),
			new TreeDecoration(MegaJungleTree::class, 15),
			new TreeDecoration(CocoaTree::class, 30)
		];
	}

	/** @var MelonDecorator */
	protected $melonDecorator;

	public function __construct(){
		$this->melonDecorator = new MelonDecorator();
		parent::__construct();
	}

	protected function initPopulators() : void{
		$this->treeDecorator->setAmount(65);
		$this->treeDecorator->setTrees(self::$TREES);
		$this->flowerDecorator->setAmount(4);
		$this->flowerDecorator->setFlowers(...self::$FLOWERS);
		$this->tallGrassDecorator->setAmount(25);
		$this->tallGrassDecorator->setFernDensity(0.25);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::JUNGLE, BiomeIds::JUNGLE_HILLS, BiomeIds::MUTATED_JUNGLE];
	}

	protected function populateOnGround(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$sourceX = $chunkX << 4;
		$sourceZ = $chunkZ << 4;

		for($i = 0; $i < 7; ++$i){
			$x = $sourceX + $random->nextBoundedInt(16);
			$z = $sourceZ + $random->nextBoundedInt(16);
			$y = $level->getChunk($chunkX, $chunkZ)->getHighestBlockAt($x & 0x0f, $z & 0x0f);
		}

		parent::populateOnGround($level, $chunkX, $chunkZ, $random);
		$this->melonDecorator->populate($level, $chunkX, $chunkZ, $random);
	}
}

JunglePopulator::init();