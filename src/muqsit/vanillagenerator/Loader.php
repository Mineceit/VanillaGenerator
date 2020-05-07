<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator;

use muqsit\vanillagenerator\generator\nether\NetherGenerator;
use muqsit\vanillagenerator\generator\overworld\OverworldGenerator;
use pocketmine\block\BlockIds;
use pocketmine\plugin\PluginBase;
use pocketmine\level\generator\GeneratorManager;

final class Loader extends PluginBase
{

	private static $transparent_blocks = [
		BlockIds::GLASS,
		BlockIds::GLASS_PANE,
		BlockIds::DANDELION,
		BlockIds::POPPY,
		BlockIds::AIR
	];

	public function onLoad() : void{
		GeneratorManager::addGenerator(NetherGenerator::class, "vanilla_nether");
		GeneratorManager::addGenerator(OverworldGenerator::class, "vanilla_overworld");
	}

	/**
	 * @param int $block
	 * @return bool
	 */
	public static function isSolid(int $block): bool
	{
		if(isset(self::$transparent_blocks[$block])){
			return false;
		}

		return true;
	}
}
