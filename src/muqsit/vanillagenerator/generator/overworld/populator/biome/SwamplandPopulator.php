<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\object\tree\SwampTree;
use muqsit\vanillagenerator\generator\overworld\biome\BiomeIds;
use muqsit\vanillagenerator\generator\overworld\decorator\MushroomDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\types\FlowerDecoration;
use muqsit\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use muqsit\vanillagenerator\generator\overworld\decorator\WaterLilyDecorator;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class SwamplandPopulator extends BiomePopulator{

	/** @var TreeDecoration[] */
	protected static $TREES;

	/** @var FlowerDecoration[] */
	protected static $FLOWERS;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(SwampTree::class, 1)
		];
	}

	protected static function initFlowers() : void{
		self::$FLOWERS = [
			new FlowerDecoration(BlockFactory::get(BlockIds::POPPY, 1), 1)
		];
	}

	/** @var MushroomDecorator */
	private $swamplandBrownMushroomDecorator;

	/** @var MushroomDecorator */
	private $swamplandRedMushroomDecorator;

	/** @var WaterLilyDecorator */
	private $waterlilyDecorator;

	public function __construct(){
		$this->swamplandBrownMushroomDecorator = new MushroomDecorator(BlockFactory::get(BlockIds::BROWN_MUSHROOM));
		$this->swamplandRedMushroomDecorator = new MushroomDecorator(BlockFactory::get(BlockIds::RED_MUSHROOM));
		$this->waterlilyDecorator = new WaterLilyDecorator();
		parent::__construct();
	}

	protected function initPopulators() : void{
		$this->sandPatchDecorator->setAmount(0);
		$this->gravelPatchDecorator->setAmount(0);
		$this->treeDecorator->setAmount(2);
		$this->treeDecorator->setTrees(...self::$TREES);
		$this->flowerDecorator->setAmount(1);
		$this->flowerDecorator->setFlowers(...self::$FLOWERS);
		$this->tallGrassDecorator->setAmount(5);
		$this->deadBushDecorator->setAmount(1);
		$this->sugarCaneDecorator->setAmount(20);
		$this->swamplandBrownMushroomDecorator->setAmount(8);
		$this->swamplandRedMushroomDecorator->setAmount(8);
		$this->waterlilyDecorator->setAmount(4);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::SWAMPLAND, BiomeIds::MUTATED_SWAMPLAND];
	}

	protected function populateOnGround(ChunkManager $world, Random $random, Chunk $chunk) : void{
		parent::populateOnGround($world, $random, $chunk);
		$this->swamplandBrownMushroomDecorator->populate($world, $random, $chunk);
		$this->swamplandRedMushroomDecorator->populate($world, $random, $chunk);
		$this->waterlilyDecorator->populate($world, $random, $chunk);
	}
}

SwamplandPopulator::init();