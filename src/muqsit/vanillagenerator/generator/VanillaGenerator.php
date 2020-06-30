<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator;

use muqsit\vanillagenerator\generator\biomegrid\MapLayer;
use muqsit\vanillagenerator\generator\noise\bukkit\OctaveGenerator;
use muqsit\vanillagenerator\generator\overworld\WorldType;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\normal\Normal;
use pocketmine\level\generator\populator\Populator as PMPopulator;
use pocketmine\level\SimpleChunkManager;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
use ReflectionProperty;

abstract class VanillaGenerator extends Generator
{

	protected const WORLD_DEPTH = 128;

	private static $GAUSSIAN_KERNEL = null;
	private static $SMOOTH_SIZE = 2;

	/** @var SimpleChunkManager */
	private static $world;

	private static function modifyChunkManager(SimpleChunkManager $world, self $generator): SimpleChunkManager
	{
		static $_worldHeight = null;
		if ($_worldHeight === null) {
			/** @noinspection PhpUnhandledExceptionInspection */
			$_worldHeight = new ReflectionProperty($world, "worldHeight");
			$_worldHeight->setAccessible(true);
		}

		$_worldHeight->setValue($world, $generator->getWorldHeight());
		self::$world = $world;

		return $world;
	}

	/** @var OctaveGenerator[] */
	private $octaveCache = [];

	/** @var Populator[] */
	private $populators = [];

	/** @var MapLayer[] */
	private $biomeGrid;

	public function __construct(array $settings = [], int $seed = 0)
	{
		self::$world = new SimpleChunkManager($seed);

		assert(self::$world instanceof SimpleChunkManager);
		$this->biomeGrid = MapLayer::initialize(self::$world->getSeed(), Environment::OVERWORLD, WorldType::NORMAL);

		if (self::$GAUSSIAN_KERNEL === null) {
			self::generateKernel();
		}
	}

	private static function generateKernel(): void
	{
		self::$GAUSSIAN_KERNEL = [];

		$bellSize = 1 / self::$SMOOTH_SIZE;
		$bellHeight = 2 * self::$SMOOTH_SIZE;

		for ($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; ++$sx) {
			self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];

			for ($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; ++$sz) {
				$bx = $bellSize * $sx;
				$bz = $bellSize * $sz;
				self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE][$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
			}
		}
	}

	/**
	 * @param int $x
	 * @param int $z
	 * @param int $sizeX
	 * @param int $sizeZ
	 * @return int[]
	 */
	public function getBiomeGridAtLowerRes(int $x, int $z, int $sizeX, int $sizeZ): array
	{
		return $this->biomeGrid[1]->generateValues($x, $z, $sizeX, $sizeZ);
	}

	/**
	 * @param int $x
	 * @param int $z
	 * @param int $sizeX
	 * @param int $sizeZ
	 * @return int[]
	 */
	public function getBiomeGrid(int $x, int $z, int $sizeX, int $sizeZ): array
	{
		return $this->biomeGrid[0]->generateValues($x, $z, $sizeX, $sizeZ);
	}

	protected function addPopulators(Populator ...$populators): void
	{
		array_push($this->populators, ...$populators);
	}

	/**
	 * @param OctaveGenerator[] $octaves
	 */
	abstract protected function createWorldOctaves(array &$octaves): void;

	public function generateChunk(int $chunkX, int $chunkZ): void
	{
		$biomes = new VanillaBiomeGrid();
		$biomeValues = $this->biomeGrid[0]->generateValues($chunkX * 16, $chunkZ * 16, 16, 16);
		for ($i = 0, $biomeValues_c = count($biomeValues); $i < $biomeValues_c; ++$i) {
			$biomes->biomes[$i] = $biomeValues[$i];
		}

		$this->generateChunkData($chunkX, $chunkZ, $biomes);
	}

	abstract protected function generateChunkData(int $chunkX, int $chunkZ, VanillaBiomeGrid $biomes): void;

	/**
	 * @return OctaveGenerator[]
	 */
	protected function getWorldOctaves(): array
	{
		if (count($this->octaveCache) === 0) {
			$this->createWorldOctaves($this->octaveCache);
		}

		return $this->octaveCache;
	}

	/**
	 * @return Populator[]
	 */
	public function getDefaultPopulators(): array
	{
		return $this->populators;
	}

	final public function populateChunk(int $chunkX, int $chunkZ): void
	{
		foreach ($this->populators as $populator) {
			$chunk = self::$world->getChunk($chunkX, $chunkZ);
			if($chunk == null) {

			}
			$populator->populate(self::$world, $chunkX, $chunkZ, $this->random);
		}
	}

	public function getWorldHeight(): int
	{
		return Level::Y_MAX;
	}
}
