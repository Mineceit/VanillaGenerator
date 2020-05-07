<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\object;

use muqsit\vanillagenerator\Loader;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockFactory;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class Cactus extends TerrainObject{

	private const FACES = [Vector3::SIDE_NORTH, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_WEST];

	/**
	 * Generates or extends a cactus, if there is space.
	 *
	 * @param ChunkManager $world
	 * @param Random $random
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @return bool
	 */
	public function generate(ChunkManager $world, Random $random, int $x, int $y, int $z) : bool{
		if($world->getBlockIdAt($x, $y, $z) === BlockIds::AIR){
			$height = $random->nextBoundedInt($random->nextBoundedInt(3) + 1) + 1;
			for($n = $y; $n < $y + $height; ++$n){
				$vec = new Vector3($x, $n, $z);
				$typeBelow = $world->getBlockAt($x, $n - 1, $z);
				if(($typeBelow === BlockIds::SAND || $typeBelow === BlockIds::CACTUS) && $world->getBlockAt($x, $n + 1, $z)->getId() === BlockIds::AIR){
					foreach(self::FACES as $face){
						$face = $vec->getSide($face);
						if(Loader::isSolid($world->getBlockIdAt($face->x, $face->y, $face->z))){
							return $n > $y;
						}
					}

					$world->setBlockIdAt($x, $n, $z, BlockIds::CACTUS);
				}
			}
		}
		return true;
	}
}