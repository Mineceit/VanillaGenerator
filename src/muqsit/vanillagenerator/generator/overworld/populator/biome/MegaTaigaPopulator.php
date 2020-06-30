<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\object\tree\MegaPineTree;
use muqsit\vanillagenerator\generator\object\tree\MegaSpruceTree;
use muqsit\vanillagenerator\generator\object\tree\RedwoodTree;
use muqsit\vanillagenerator\generator\object\tree\TallRedwoodTree;
use muqsit\vanillagenerator\generator\overworld\biome\BiomeIds;
use muqsit\vanillagenerator\generator\overworld\decorator\StoneBoulderDecorator;
use muqsit\vanillagenerator\generator\overworld\decorator\types\TreeDecoration;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class MegaTaigaPopulator extends TaigaPopulator{

	/** @var TreeDecoration[] */
	protected static $TREES;

	protected static function initTrees() : void{
		self::$TREES = [
			new TreeDecoration(RedwoodTree::class, 52),
			new TreeDecoration(TallRedwoodTree::class, 26),
			new TreeDecoration(MegaPineTree::class, 36),
			new TreeDecoration(MegaSpruceTree::class, 3)
		];
	}

	public function getBiomes() : ?array{
		return [BiomeIds::REDWOOD_TAIGA, BiomeIds::REDWOOD_TAIGA_HILLS];
	}

	/** @var StoneBoulderDecorator */
	protected $stoneBoulderDecorator;

	public function __construct(){
		parent::__construct();
		$this->stoneBoulderDecorator = new StoneBoulderDecorator();
	}

	protected function initPopulators() : void{
		$this->treeDecorator->setTrees(...self::$TREES);
		$this->tallGrassDecorator->setAmount(7);
		$this->deadBushDecorator->setAmount(0);
		$this->taigaBrownMushroomDecorator->setAmount(3);
		$this->taigaRedMushroomDecorator->setAmount(3);
	}

	protected function populateOnGround(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$this->stoneBoulderDecorator->populate($level, $chunkX, $chunkZ, $random);
		parent::populateOnGround($level, $chunkX, $chunkZ, $random);
	}
}

MegaTaigaPopulator::init();