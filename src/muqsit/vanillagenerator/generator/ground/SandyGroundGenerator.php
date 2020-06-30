<?php /** @noinspection MagicMethodsValidityInspection */

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\ground;

use pocketmine\block\Block;

class SandyGroundGenerator extends GroundGenerator{

	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct(){
		$this->setTopMaterial(Block::SAND);
		$this->setGroundMaterial(Block::SAND);
	}
}