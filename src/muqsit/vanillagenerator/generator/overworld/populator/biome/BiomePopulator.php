<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\object\tree\BigOakTree;
use muqsit\vanillagenerator\generator\object\tree\GenericTree;
use muqsit\vanillagenerator\generator\overworld\decorator\CactusDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\DeadBushDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\DoublePlantDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\FlowerDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\LakeDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\MushroomDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\PumpkinDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\SugarCaneDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\SurfaceCaveDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\TallGrassDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\TreeDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\types\FlowerDecoration;
use muqsit\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use muqsit\vanillagenerator\generator\overworld\decorator\UnderwaterDecorator;
use muqsit\vanillagenerator\generator\Populator;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class BiomePopulator implements Populator
{

	/** @var TreeDecoration[] */
	protected static $TREES;

	/** @var FlowerDecoration[] */
	protected static $FLOWERS;

	public static function init() : void{
		static::initTrees();
		static::initFlowers();
	}

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(BigOakTree::class, 1),
			new TreeDecoration(GenericTree::class, 9)
		];
	}

	protected static function initFlowers() : void{
		self::$FLOWERS = [
			new FlowerDecoration(BlockFactory::get(BlockIds::DANDELION), 2),
			new FlowerDecoration(BlockFactory::get(BlockIds::POPPY), 1)
		];
	}

	/** @var LakeDecorator */
	protected $waterLakeDecorator;

	/** @var LakeDecorator */
	protected $lavaLakeDecorator;

	/** @var OrePopulator */
	protected $orePopulator;

	/** @var UnderwaterDecorator */
	protected $sandPatchDecorator;

	/** @var UnderwaterDecorator */
	protected $clayPatchDecorator;

	/** @var UnderwaterDecorator */
	protected $gravelPatchDecorator;

	/** @var DoublePlantDecorator */
	protected $doublePlantDecorator;

	/** @var TreeDecorator */
	protected $treeDecorator;

	/** @var FlowerDecorator */
	protected $flowerDecorator;

	/** @var TallGrassDecorator */
	protected $tallGrassDecorator;

	/** @var DeadBushDecorator */
	protected $deadBushDecorator;

	/** @var MushroomDecorator */
	protected $brownMushroomDecorator;

	/** @var MushroomDecorator */
	protected $redMushroomDecorator;

	/** @var SugarCaneDecorator */
	protected $sugarCaneDecorator;

	/** @var PumpkinDecorator */
	protected $pumpkinDecorator;

	/** @var CactusDecorator */
	protected $cactusDecorator;

	/** @var SurfaceCaveDecorator */
	protected $surfaceCaveDecorator;

	/** @var Populator[] */
	private $inGroundPopulators = [];

	/** @var Populator[] */
	private $onGroundPopulators = [];

	/**
	 * Creates a populator for lakes; dungeons; caves; ores; sand, gravel and clay patches; desert
	 * wells; and vegetation.
	 */
	public function __construct(){
		$block_factory = new BlockFactory();

		$this->waterLakeDecorator = new LakeDecorator($block_factory->get(BlockIds::STILL_WATER), 4);
		$this->lavaLakeDecorator = new LakeDecorator($block_factory->get(BlockIds::STILL_LAVA), 8, 8);
		$this->orePopulator = new OrePopulator();
		$this->sandPatchDecorator = new UnderwaterDecorator($block_factory->get(BlockIds::SAND));
		$this->clayPatchDecorator = new UnderwaterDecorator($block_factory->get(BlockIds::CLAY_BLOCK));
		$this->gravelPatchDecorator = new UnderwaterDecorator($block_factory->get(BlockIds::GRAVEL));
		$this->doublePlantDecorator = new DoublePlantDecorator();
		$this->treeDecorator = new TreeDecorator();
		$this->flowerDecorator = new FlowerDecorator();
		$this->tallGrassDecorator = new TallGrassDecorator();
		$this->deadBushDecorator = new DeadBushDecorator();
		$this->brownMushroomDecorator = new MushroomDecorator($block_factory->get(BlockIds::RED_MUSHROOM));
		$this->redMushroomDecorator = new MushroomDecorator($block_factory->get(BlockIds::BROWN_MUSHROOM));
		$this->sugarCaneDecorator = new SugarCaneDecorator();
		$this->pumpkinDecorator = new PumpkinDecorator();
		$this->cactusDecorator = new CactusDecorator();
		$this->surfaceCaveDecorator = new SurfaceCaveDecorator();

		array_push($this->inGroundPopulators,
			$this->waterLakeDecorator,
			$this->lavaLakeDecorator,
			$this->surfaceCaveDecorator,
			$this->orePopulator,
			$this->sandPatchDecorator,
			$this->clayPatchDecorator,
			$this->gravelPatchDecorator
		);

		array_push($this->onGroundPopulators,
			$this->doublePlantDecorator,
			$this->treeDecorator,
			$this->flowerDecorator,
			$this->tallGrassDecorator,
			$this->deadBushDecorator,
			$this->brownMushroomDecorator,
			$this->redMushroomDecorator,
			$this->sugarCaneDecorator,
			$this->pumpkinDecorator,
			$this->cactusDecorator
		);

		$this->initPopulators();
	}

	protected function initPopulators() : void{
		$this->waterLakeDecorator->setAmount(1);
		$this->lavaLakeDecorator->setAmount(1);
		$this->surfaceCaveDecorator->setAmount(1);
		$this->sandPatchDecorator->setAmount(3);
		$this->sandPatchDecorator->setRadii(7, 2);
		$this->sandPatchDecorator->setOverridableBlocks(BlockFactory::get(BlockIds::DIRT), BlockFactory::get(BlockIds::GRASS));
		$this->clayPatchDecorator->setAmount(1);
		$this->clayPatchDecorator->setRadii(4, 1);
		$this->clayPatchDecorator->setOverridableBlocks(BlockFactory::get(BlockIds::DIRT));
		$this->gravelPatchDecorator->setAmount(1);
		$this->gravelPatchDecorator->setRadii(6, 2);
		$this->gravelPatchDecorator->setOverridableBlocks(BlockFactory::get(BlockIds::DIRT), BlockFactory::get(BlockIds::GRASS));

		$this->doublePlantDecorator->setAmount(0);
		$this->treeDecorator->setAmount(PHP_INT_MIN);
		$this->treeDecorator->setTrees(...static::$TREES);
		$this->flowerDecorator->setAmount(2);
		$this->flowerDecorator->setFlowers(...static::$FLOWERS);
		$this->tallGrassDecorator->setAmount(1);
		$this->deadBushDecorator->setAmount(0);
		$this->brownMushroomDecorator->setAmount(1);
		$this->brownMushroomDecorator->setDensity(0.25);
		$this->redMushroomDecorator->setAmount(1);
		$this->redMushroomDecorator->setDensity(0.125);
		$this->sugarCaneDecorator->setAmount(10);
		$this->cactusDecorator->setAmount(0);
	}

	/**
	 * Returns an array of biome ids or null if this populator targets all
	 * biomes.
	 *
	 * @return int[]|null
	 */
	public function getBiomes() : ?array{
		return null;
	}

	public function populate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$this->populateInGround($world, $random, $chunk);
		$this->populateOnGround($world, $random, $chunk);
	}

	protected function populateInGround(ChunkManager $world, Random $random, Chunk $chunk) : void{
		foreach($this->inGroundPopulators as $populator){
			$populator->populate($world, $random, $chunk);
		}
	}

	protected function populateOnGround(ChunkManager $world, Random $random, Chunk $chunk) : void{
		foreach($this->onGroundPopulators as $populator){
			$populator->populate($world, $random, $chunk);
		}
	}
}

BiomePopulator::init();