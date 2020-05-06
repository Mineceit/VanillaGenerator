<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use muqsit\vanillagenerator\generator\object\BlockPatch;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;

class UnderwaterDecorator extends Decorator{

	/** @var Block */
	private $type;

	/** @var int */
	private $horizRadius;

	/** @var int */
	private $vertRadius;

	/** @var int[] */
	private $overridables;

	public function __construct(Block $type){
		$this->type = $type;
	}

	/**
	 * Updates the size of this decorator.
	 *
	 * @param int $horizRadius the maximum radius on the horizontal plane
	 * @param int $vertRadius the depth above and below the center
	 * @return UnderwaterDecorator this, updated
	 */
	final public function setRadii(int $horizRadius, int $vertRadius) : UnderwaterDecorator{
		$this->horizRadius = $horizRadius;
		$this->vertRadius = $vertRadius;
		return $this;
	}

	final public function setOverridableBlocks(Block ...$overridables) : UnderwaterDecorator{
		foreach($overridables as $overridable){
			$this->overridables[] = $overridable->getId();
		}
		return $this;
	}

	public function decorate(ChunkManager $world, Random $random, Chunk $chunk) : void{
		$sourceX = ($chunk->getX() << 4) + $random->nextBoundedInt(16);
		$sourceZ = ($chunk->getZ() << 4) + $random->nextBoundedInt(16);
		$sourceY = $chunk->getHighestBlockAt($sourceX & 0x0f, $sourceZ & 0x0f) - 1;
		while($world->getBlockIdAt($sourceX, $sourceY - 1, $sourceZ) === BlockIds::STILL_WATER || ($world->getBlockIdAt($sourceX, $sourceY - 1, $sourceZ) === BlockIds::WATER && $sourceY > 1)){
			--$sourceY;
		}
		$material = $world->getBlockIdAt($sourceX, $sourceY, $sourceZ);
		if($material === BlockIds::STILL_WATER || $material === BlockIds::WATER){
			(new BlockPatch($this->type, $this->horizRadius, $this->vertRadius, ...$this->overridables))->generate($world, $random, $sourceX, $sourceY, $sourceZ);
		}
	}
}