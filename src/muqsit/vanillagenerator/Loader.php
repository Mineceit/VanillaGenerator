<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator;

use muqsit\vanillagenerator\generator\nether\NetherGenerator;
use muqsit\vanillagenerator\generator\overworld\OverworldGenerator;
use pocketmine\plugin\PluginBase;
use pocketmine\level\generator\GeneratorManager;

final class Loader extends PluginBase{

	public function onLoad() : void{
		GeneratorManager::addGenerator(NetherGenerator::class, "vanilla_nether");
		GeneratorManager::addGenerator(OverworldGenerator::class, "vanilla_overworld");
	}
}
