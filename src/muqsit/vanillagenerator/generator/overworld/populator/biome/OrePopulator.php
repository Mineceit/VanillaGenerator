<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\object\OreType;
use muqsit\vanillagenerator\generator\object\OreVein;
use muqsit\vanillagenerator\generator\Populator;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class OrePopulator extends Populator{

	/** @var OreType[][]|int[][] */
	private $ores = [];

	/**
	 * Creates a populator for dirt, gravel, andesite, diorite, granite; and coal, iron, gold,
	 * redstone, diamond and lapis lazuli ores.
	 */
	public function __construct(){
		$this->addOre(new OreType(BlockFactory::get(BlockIds::DIRT), 0, 256, 32), 10);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::GRAVEL), 0, 256, 32), 8);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::STONE, 5), 0, 80, 32), 10);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::STONE, 3), 0, 80, 32), 10);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::STONE, 1), 0, 80, 32), 10);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::COAL_ORE), 0, 128, 16), 20);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::IRON_ORE), 0, 64, 12), 20);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::GOLD_ORE), 0, 32, 12), 2);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::REDSTONE_ORE), 0, 16, 12), 8);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::DIAMOND_ORE), 0, 16, 12), 1);
		$this->addOre(new OreType(BlockFactory::get(BlockIds::LAPIS_ORE), 16, 16, 12), 1);
	}

	protected function addOre(OreType $type, int $value) : void{
		$this->ores[] = [$type, $value];
	}

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$cx = $chunkX << 4;
		$cz = $chunkZ << 4;

		/**
		 * @var OreType $oreType
		 * @var int $value
		 */
		foreach($this->ores as [$oreType, $value]){
			for($n = 0; $n < $value; ++$n){
				$sourceX = $cx + $random->nextBoundedInt(16);
				$sourceZ = $cz + $random->nextBoundedInt(16);
				$sourceY = $oreType->getRandomHeight($random);

				(new OreVein($oreType))->generate($level, $random, $sourceX, $sourceY, $sourceZ);
			}
		}
	}
}