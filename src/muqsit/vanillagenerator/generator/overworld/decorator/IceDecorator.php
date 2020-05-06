<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use muqsit\vanillagenerator\generator\object\BlockPatch;
use muqsit\vanillagenerator\generator\object\IceSpike;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockFactory;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class IceDecorator extends Decorator{

	/** @var int[] */
	private static $OVERRIDABLES;

	public static function init() : void{
		self::$OVERRIDABLES = [
			BlockFactory::get(BlockIds::DIRT),
			BlockFactory::get(BlockIds::GRASS),
			BlockFactory::get(BlockIds::SNOW),
			BlockFactory::get(BlockIds::ICE)
		];
	}

	public function populate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$sourceX = $chunk->getX() << 4;
		$sourceZ = $chunk->getZ() << 4;

		for($i = 0; $i < 3; ++$i){
			$x = $sourceX + $random->nextBoundedInt(16);
			$z = $sourceZ + $random->nextBoundedInt(16);
			$y = $chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f) - 1;
			while($y > 2 && $world->getBlockIdAt($x, $y, $z) === BlockIds::AIR){
				--$y;
			}
			if($world->getBlockIdAt($x, $y, $z) === BlockIds::SNOW_BLOCK){
				(new BlockPatch(BlockFactory::get(BlockIds::PACKED_ICE), 4, 1, ...self::$OVERRIDABLES))->generate($world, $random, $x, $y, $z);
			}
		}

		for($i = 0; $i < 2; ++$i){
			$x = $sourceX + $random->nextBoundedInt(16);
			$z = $sourceZ + $random->nextBoundedInt(16);
			$y = $chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f);
			while($y > 2 && $world->getBlockIdAt($x, $y, $z) === BlockIds::AIR){
				--$y;
			}
			if($world->getBlockAt($x, $y, $z) === BlockIds::SNOW_BLOCK){
				(new IceSpike())->generate($world, $random, $x, $y, $z);
			}
		}
	}

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
	}
}

IceDecorator::init();