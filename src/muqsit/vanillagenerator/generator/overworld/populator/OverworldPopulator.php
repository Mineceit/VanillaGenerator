<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\populator;

use muqsit\vanillagenerator\generator\overworld\biome\BiomeIds;
use muqsit\vanillagenerator\generator\overworld\populator\biome\BiomePopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\BirchForestMountainsPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\BirchForestPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\DesertMountainsPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\DesertPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\FlowerForestPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\ForestPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\IcePlainsPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\IcePlainsSpikesPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\JungleEdgePopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\JunglePopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\MegaSpruceTaigaPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\MegaTaigaPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\PlainsPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\RoofedForestPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\SavannaMountainsPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\SavannaPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\SunflowerPlainsPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\SwamplandPopulator;
use muqsit\vanillagenerator\generator\overworld\populator\biome\TaigaPopulator;
use muqsit\vanillagenerator\generator\Populator;
use pocketmine\Server;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use ReflectionClass;

class OverworldPopulator extends Populator{

	/** @var Populator[] */
	private $biomePopulators = []; // key = biomeId

	/**
	 * Creates a populator with biome populators for all vanilla overworld biomes.
	 */
	public function __construct(){
		$this->registerBiomePopulator(new BiomePopulator()); // defaults applied to all biomes
		$this->registerBiomePopulator(new PlainsPopulator());
		$this->registerBiomePopulator(new SunflowerPlainsPopulator());
		$this->registerBiomePopulator(new ForestPopulator());
		$this->registerBiomePopulator(new BirchForestPopulator());
		$this->registerBiomePopulator(new BirchForestMountainsPopulator());
		$this->registerBiomePopulator(new RoofedForestPopulator());
		$this->registerBiomePopulator(new FlowerForestPopulator());
		$this->registerBiomePopulator(new DesertPopulator());
		$this->registerBiomePopulator(new DesertMountainsPopulator());
		$this->registerBiomePopulator(new JunglePopulator());
		$this->registerBiomePopulator(new JungleEdgePopulator());
		$this->registerBiomePopulator(new SwamplandPopulator());
		$this->registerBiomePopulator(new TaigaPopulator());
		$this->registerBiomePopulator(new MegaTaigaPopulator());
		$this->registerBiomePopulator(new MegaSpruceTaigaPopulator());
		$this->registerBiomePopulator(new IcePlainsPopulator());
		$this->registerBiomePopulator(new IcePlainsSpikesPopulator());
		$this->registerBiomePopulator(new SavannaPopulator());
		$this->registerBiomePopulator(new SavannaMountainsPopulator());
		/*
		$this->registerBiomePopulator(new ExtremeHillsPopulator());
		$this->registerBiomePopulator(new ExtremeHillsPlusPopulator());
		$this->registerBiomePopulator(new MesaPopulator());
		$this->registerBiomePopulator(new MesaForestPopulator());
		$this->registerBiomePopulator(new MushroomIslandPopulator());
		$this->registerBiomePopulator(new OceanPopulator());
		*/
	}

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$chunk = $level->getChunk($chunkX, $chunkZ);
		if(is_null($chunk)) {
			return;
		}

		$biome = $chunk->getBiomeId(8, 8);
		if(isset($this->biomePopulators[$biome])){
			$this->biomePopulators[$biome]->populate($level, $chunkX, $chunkZ, $random);
		}
	}

	private function registerBiomePopulator(BiomePopulator $populator) : void{
		$biomes = $populator->getBiomes();
		if($biomes === null){
			$biomes = array_values((new ReflectionClass(BiomeIds::class))->getConstants());
		}

		foreach($biomes as $biome){
			$this->biomePopulators[$biome] = $populator;
		}
	}
}