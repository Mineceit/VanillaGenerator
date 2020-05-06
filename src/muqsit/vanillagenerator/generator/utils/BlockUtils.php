<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\utils;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockLegacyMetadata;
use pocketmine\block\utils\BlockDataSerializer;
use pocketmine\block\Vine;
use pocketmine\math\Vector3;

final class BlockUtils
{

	public static function VINE(int $face) : Block
	{
		static $meta = [
			Vector3::SIDE_NORTH => Vine::FLAG_NORTH,
			Vector3::SIDE_SOUTH => Vine::FLAG_SOUTH,
			Vector3::SIDE_EAST => Vine::FLAG_EAST,
			Vector3::SIDE_WEST => Vine::FLAG_WEST
		];

		return BlockFactory::get(BlockIds::VINE, $meta[$face]);
	}

	public static function COCOA(int $face, int $age = 0) : Block
	{
		return BlockFactory::get(BlockIds::COCOA, Vector3::getOppositeSide($face));
	}
}