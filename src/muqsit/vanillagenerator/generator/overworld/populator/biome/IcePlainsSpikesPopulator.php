<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator\biome;

use muqsit\vanillagenerator\generator\overworld\biome\BiomeIds;
use muqsit\vanillagenerator\generator\overworld\decorator\IceDecorator;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class IcePlainsSpikesPopulator extends IcePlainsPopulator{

	/** @var IceDecorator */
	protected $iceDecorator;

	public function __construct(){
		parent::__construct();
		$this->tallGrassDecorator->setAmount(0);
		$this->iceDecorator = new IceDecorator();
	}

	protected function populateOnGround(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$this->iceDecorator->populate($world, $random, $chunk);
		parent::populateOnGround($world, $random, $chunk);
	}

	public function getBiomes() : ?array{
		return [BiomeIds::MUTATED_ICE_FLATS];
	}
}