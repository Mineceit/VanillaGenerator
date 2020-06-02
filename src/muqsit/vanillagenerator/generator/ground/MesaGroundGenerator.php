<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\ground;

use muqsit\vanillagenerator\generator\noise\glowstone\SimplexOctaveGenerator;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\level\ChunkManager;

class MesaGroundGenerator extends GroundGenerator{

	public const NORMAL = 0;
	public const BRYCE = 1;
	public const FOREST = 2;

	/** @var int */
	private $type;

	/** @var int[] */
	private $colorLayer;

	/** @var Block */
	private $topMaterial;

	/** @var Block */
	private $groundMaterial;

	/** @var SimplexOctaveGenerator */
	private $colorNoise;

	/** @var SimplexOctaveGenerator */
	private $canyonHeightNoise;

	/** @var SimplexOctaveGenerator */
	private $canyonScaleNoise;

	/** @var int */
	private $seed;

	/** @noinspection MagicMethodsValidityInspection */
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct(int $type = self::NORMAL){
		$this->type = $type;
		$this->topMaterial = VanillaBlocks::RED_SAND();
		$this->groundMaterial = VanillaBlocks::ORANGE_STAINED_CLAY();
		$this->colorLayer = array_fill(0, 64, 0);
	}

	private function initialize(int $seed) : void{
		if($seed !== $this->seed || $this->colorNoise === null || $this->canyonScaleNoise === null || $this->canyonHeightNoise === null){
			$random = new Random($seed);
			$this->colorNoise = SimplexOctaveGenerator::fromRandomAndOctaves($random, 1, 0, 0, 0);
			$this->colorNoise->setScale(1 / 512.0);
			$this->initializeColorLayers($random);

			$this->canyonHeightNoise = SimplexOctaveGenerator::fromRandomAndOctaves($random, 4, 0, 0, 0);
			$this->canyonHeightNoise->setScale(1 / 4.0);
			$this->canyonScaleNoise = SimplexOctaveGenerator::fromRandomAndOctaves($random, 1, 0, 0, 0);
			$this->canyonScaleNoise->setScale(1 / 512.0);
			$this->seed = $seed;
		}
	}

	public function generateTerrainColumn(ChunkManager $world, Random $random, int $x, int $z, int $biome, float $surfaceNoise) : void{
		$this->initialize($random->getSeed());
		$seaLevel = 64;

		$topMat = $this->topMaterial;
		$groundMat = $this->groundMaterial;

		$surfaceHeight = max((int) ($surfaceNoise / 3.0 + 3.0 + $random->nextFloat() * 0.25), 1);
		$colored = cos($surfaceNoise / 3.0 * M_PI) <= 0;
		$bryceCanyonHeight = 0.0;
		if($this->type === self::BRYCE){
			$noiseX = ($x & 0xFFFFFFF0) + ($z & 0xF);
			$noiseZ = ($z & 0xFFFFFFF0) + ($x & 0xF);
			$noiseCanyonHeight = min(abs($surfaceNoise), $this->canyonHeightNoise->noise($noiseX, $noiseZ, 0, 0.5, 2.0, false));
			if($noiseCanyonHeight > 0){
				$heightScale = abs($this->canyonScaleNoise->noise($noiseX, $noiseZ, 0, 0.5, 2.0, false));
				$bryceCanyonHeight = ($noiseCanyonHeight ** 2) * 2.5;
				$maxHeight = ceil(50 * $heightScale) + 14;
				if($bryceCanyonHeight > $maxHeight){
					$bryceCanyonHeight = $maxHeight;
				}
				$bryceCanyonHeight += $seaLevel;
			}
		}

		$chunkX = $x;
		$chunkZ = $z;

		$deep = -1;
		$groundSet = false;

		$grass = VanillaBlocks::GRASS();
		$coarse_dirt = VanillaBlocks::DIRT();

		for($y = 255; $y >= 0; --$y){
			if($y < (int) $bryceCanyonHeight && $world->getBlockIdAt($x, $y, $z) === BlockLegacyIds::AIR){
				$world->setBlockIdAt($x, $y, $z, BlockFactory::get(Block::STONE)->getId());
			}
			if($y <= $random->nextBoundedInt(5)){
				$world->setBlockIdAt($x, $y, $z, VanillaBlocks::BEDROCK()->getId());
			}else{
				$matId = $world->getBlockIdAt($x, $y, $z);
				if($matId === BlockLegacyIds::AIR){
					$deep = -1;
				}elseif($matId === BlockLegacyIds::STONE){
					if($deep === -1){
						$groundSet = false;
						if($y >= $seaLevel - 5 && $y <= $seaLevel){
							$groundMat = $this->groundMaterial;
						}

						$deep = $surfaceHeight + max(0, $y - $seaLevel - 1);
						if($y >= $seaLevel - 2){
							if($this->type === self::FOREST && $y > $seaLevel + 22 + ($surfaceHeight << 1)){
								$topMat = $colored ? $grass : $coarse_dirt;
								$world->setBlockIdAt($x, $y, $z, $topMat->getId());
							}elseif($y > $seaLevel + 2 + $surfaceHeight){
								$color = $this->colorLayer[($y + (int) round(
										$this->colorNoise->noise($chunkX, $chunkZ, 0, 0.5, 2.0, false) * 2.0))
								% count($this->colorLayer)];
								$this->setColoredGroundLayer($world, $x, $y, $z, $y < $seaLevel || $y > 128 ? 1 : ($colored ? $color : -1));
							}else{
								$world->setBlockIdAt($x, $y, $z, $this->topMaterial->getId());
								$groundSet = true;
							}
						}else{
							$world->setBlockIdAt($x, $y, $z, $groundMat->getId());
						}
					}elseif($deep > 0){
						--$deep;
						if($groundSet){
							$world->setBlockIdAt($x, $y, $z, $this->groundMaterial->getId());
						}else{
							$color = $this->colorLayer[($y + (int) round(
									$this->colorNoise->noise($chunkX, $chunkZ, 0, 0.5, 2.0, false) * 2.0))
							% count($this->colorLayer)];
							$this->setColoredGroundLayer($world, $x, $y, $z, $color);
						}
					}
				}
			}
		}
	}

	private function setColoredGroundLayer(ChunkManager $world, int $x, int $y, int $z, int $color) : void{
		$world->setBlockIdAt($x, $y, $z, VanillaBlocks::HARDENED_CLAY()->getId());
	}

	private function setRandomLayerColor(Random $random, int $minLayerCount, int $minLayerHeight, int $color) : void{
		for($i = 0; $i < $random->nextBoundedInt(4) + $minLayerCount; ++$i){
			$j = $random->nextBoundedInt(count($this->colorLayer));
			$k = 0;
			while($k < $random->nextBoundedInt(3) + $minLayerHeight && $j < count($this->colorLayer)){
				$this->colorLayer[$j++] = $color;
				++$k;
			}
		}
	}

	private function initializeColorLayers(Random $random) : void{
		foreach($this->colorLayer as $k => $_){
			$this->colorLayer[$k] = -1; // hard clay, other values are stained clay
		}
		$i = 0;
		while($i < count($this->colorLayer)){
			$i += $random->nextBoundedInt(5) + 1;
			if($i < count($this->colorLayer)){
				$this->colorLayer[$i++] = 1; // orange
			}
		}
		$this->setRandomLayerColor($random, 2, 1, 4); // yellow
		$this->setRandomLayerColor($random, 2, 2, 12); // brown
		$this->setRandomLayerColor($random, 2, 1, 14); // red
		$j = 0;
		for($i = 0; $i < $random->nextBoundedInt(3) + 3; ++$i){
			$j += $random->nextBoundedInt(16) + 4;
			if($j >= count($this->colorLayer)){
				break;
			}
			if($random->nextBoundedInt(2) === 0 || ($j < count($this->colorLayer) - 1 && $random->nextBoundedInt(2) === 0)){
				$this->colorLayer[$j - 1] = 8; // light gray
			}else{
				$this->colorLayer[$j] = 0; // white
			}
		}
	}
}