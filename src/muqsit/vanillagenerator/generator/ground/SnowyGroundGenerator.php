<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\ground;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;

class SnowyGroundGenerator extends GroundGenerator{

	/** @noinspection MagicMethodsValidityInspection */
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct(){
		$this->setTopMaterial(Block::SNOW);
	}
}