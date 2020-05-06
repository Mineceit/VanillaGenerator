<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class MushroomDecorator extends Decorator{

	/** @var Block */
	private $type;

	/** @var bool */
	private $fixedHeightRange = false;

	/** @var float */
	private $density = 0.0;

	/**
	 * Creates a mushroom decorator for the overworld.
	 *
	 * @param Block $type {@link Material#BROWN_MUSHROOM} or {@link Material#RED_MUSHROOM}
	 */
	public function __construct(Block $type){
		$this->type = $type;
	}

	public function setUseFixedHeightRange() : MushroomDecorator{
		$this->fixedHeightRange = true;
		return $this;
	}

	public function setDensity(float $density) : MushroomDecorator{
		$this->density = $density;
		return $this;
	}

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		if($random->nextFloat() < $this->density){
			$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
			$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
			$sourceY = $chunk->getHighestBlockAt($sourceX & 0x0f, $sourceZ & 0x0f);
			$sourceY = $this->fixedHeightRange ? $sourceY : $random->nextBoundedInt($sourceY << 1);

			$height = $world->getWorldHeight();
			for($i = 0; $i < 64; ++$i){
				$x = $sourceX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
				$z = $sourceZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
				$y = $sourceY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);

				$block = $world->getBlockIdAt($x, $y, $z);
				$blockLight = $world->getBlockLightAt($x, $y, $z);
				$blockBelow = $world->getBlockIdAt($x, $y - 1, $z);
				$blockDataBelow = $world->getBlockDataAt($x, $y - 1, $z);
				if($y < $height && $block === BlockIds::AIR){
					switch($blockBelow){
						case BlockIds::MYCELIUM:
						case BlockIds::PODZOL:
							$canPlaceShroom = true;
							break;
						case BlockIds::GRASS:
							$canPlaceShroom = ($blockLight < 13);
							break;
						case BlockIds::DIRT:
							if($blockDataBelow === BlockIds::DIRT){
								$canPlaceShroom = $blockLight < 13;
							}else{
								$canPlaceShroom = false;
							}
							break;
						default:
							$canPlaceShroom = false;
					}
					if($canPlaceShroom){
						$world->setBlockIdAt($x, $y, $z, $this->type->getId());
					}
				}
			}
		}
	}
}