<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\nether\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class GlowstoneDecorator extends Decorator{

	private const SIDES = [Vector3::SIDE_EAST, Vector3::SIDE_WEST, Vector3::SIDE_DOWN, Vector3::SIDE_UP, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH];

	/** @var bool */
	private $variableAmount;

	public function __construct(bool $variableAmount = false){
		$this->variableAmount = $variableAmount;
	}

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$amount = $this->variableAmount ? 1 + $random->nextBoundedInt(1 + $random->nextBoundedInt(10)) : 10;

		$height = $world->getWorldHeight();
		$sourceYMargin = 8 * ($height >> 7);

		for($i = 0; $i < $amount; ++$i){
			$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
			$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
			$sourceY = 4 + $random->nextBoundedInt($height - $sourceYMargin);

			$block = $world->getBlockIdAt($sourceX, $sourceY, $sourceZ);
			if(
				$block !== BlockLegacyIds::AIR ||
				$world->getBlockIdAt($sourceX, $sourceY + 1, $sourceZ) !== BlockLegacyIds::NETHERRACK
			){
				continue;
			}

			$world->setBlockIdAt($sourceX, $sourceY, $sourceZ, VanillaBlocks::GLOWSTONE()->getId());

			for($j = 0; $j < 1500; ++$j){
				$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
				$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
				$y = $sourceY - $random->nextBoundedInt(12);
				$block = $world->getBlockIdAt($x, $y, $z);
				if($block !== BlockLegacyIds::AIR){
					continue;
				}

				$glowstoneBlockCount = 0;
				$vector = new Vector3($x, $y, $z);
				foreach(self::SIDES as $face){
					$pos = $vector->getSide($face);
					if($world->getBlockIdAt($pos->x, $pos->y, $pos->z) === BlockLegacyIds::GLOWSTONE){
						++$glowstoneBlockCount;
					}
				}

				if($glowstoneBlockCount === 1){
					$world->setBlockIdAt($x, $y, $z, VanillaBlocks::GLOWSTONE()->getId());
				}
			}
		}
	}
}