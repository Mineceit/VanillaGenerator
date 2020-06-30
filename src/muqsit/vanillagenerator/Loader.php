<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator;

use muqsit\vanillagenerator\generator\nether\NetherGenerator;
use muqsit\vanillagenerator\generator\overworld\OverworldGenerator;
use pocketmine\block\BlockIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
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
	 * @param CommandSender $sender
	 * @param Command $command
	 * @param string $label
	 * @param array $args
	 * @return bool|mixed
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		switch($command->getName()){
			case "generators":
				$sender->sendMessage("Known Generators:");

				$list = GeneratorManager::getGeneratorList();
				foreach($list as $str){
					$sender->sendMessage($str);
				}
				break;
		}

		return true;
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
