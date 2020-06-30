<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\object\DoubleTallPlant;
use muqsit\vanillagenerator\generator\object\tree\BirchTree;
use muqsit\vanillagenerator\generator\object\tree\GenericTree;
use muqsit\vanillagenerator\generator\overworld\biome\BiomeIds;
use muqsit\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class ForestPopulator extends BiomePopulator
{

	private const BIOMES = [BiomeIds::FOREST, BiomeIds::FOREST_HILLS];

	/** @var TreeDecoration[] */
	protected static $TREES;

	/** @var Block[] */
	private static $DOUBLE_PLANTS;

	public static function init() : void{
		parent::init();
		self::$DOUBLE_PLANTS = [];
	}

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(GenericTree::class, 4),
			new TreeDecoration(BirchTree::class, 1)
		];
	}

	/** @var int */
	protected $doublePlantLoweringAmount = 3;

	protected function initPopulators() : void{
		$this->doublePlantDecorator->setAmount(0);
		$this->treeDecorator->setAmount(10);
		$this->treeDecorator->setTrees(...self::$TREES);
		$this->tallGrassDecorator->setAmount(2);
	}

	public function getBiomes() : ?array{
		return self::BIOMES;
	}

	public function populateOnGround(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$sourceX = $chunk->getX() << 4;
		$sourceZ = $chunk->getZ() << 4;
		$amount = $random->nextBoundedInt(5) - $this->doublePlantLoweringAmount;
		$i = 0;
		while($i < $amount){
			for($j = 0; $j < 5; ++$j, ++$i){
				$x = $sourceX + $random->nextBoundedInt(16);
				$z = $sourceZ + $random->nextBoundedInt(16);
				$y = $random->nextBoundedInt($chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f) + 32);
				$species = self::$DOUBLE_PLANTS[$random->nextBoundedInt(count(self::$DOUBLE_PLANTS))];
				if((new DoubleTallPlant($species))->generate($world, $random, $x, $y, $z)){
					++$i;
					break;
				}
			}
		}

		parent::populateOnGround($level, $chunkX, $chunkZ, $random);
	}
}

ForestPopulator::init();