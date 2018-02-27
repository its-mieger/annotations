<?php


	namespace ItsMiegerAnnotationsTests\Cases\Provider;


	use Illuminate\Support\Facades\Artisan;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\Event;
	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Cache\MemoryCache;
	use ItsMieger\Annotations\Cache\PhpCache;
	use ItsMieger\Annotations\Contracts\AnnotationCache;
	use ItsMieger\Annotations\Parser\AnnotationParser;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;
	use ItsMieger\Annotations\Reader\AnnotationReader;
	use ItsMieger\Annotations\Reader\CachedReader;
	use ItsMiegerAnnotationsTests\Cases\TestCase;
	use PHPUnit\Framework\MockObject\MockObject;

	class AnnotationsServiceProviderTest extends TestCase
	{
		public function testManagerRegistered() {

			$resolved = resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.manager');

			$this->assertInstanceOf(AnnotationsManager::class, $resolved);
			$this->assertSame($resolved, resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.manager'));
		}

		public function testParserRegistered() {

			$resolved = resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.parser');

			$this->assertInstanceOf(AnnotationParser::class, $resolved);
			$this->assertSame($resolved, resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.parser'));
		}

		public function testCacheRegistered() {
			$resolved = resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.cache');

			$this->assertInstanceOf(PhpCache::class, $resolved);
			$this->assertSame($resolved, resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.cache'));
		}

		public function testDefaultReaderRegistered() {
			/** @var CachedReader $resolved */
			$resolved = resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.reader');

			$this->assertInstanceOf(CachedReader::class, $resolved);
			$this->assertInstanceOf(AnnotationReader::class, $resolved->getReader());
			$this->assertSame($resolved, resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.reader'));
		}

		public function testInheritedReaderRegistered() {
			/** @var CachedReader $resolved */
			$resolved = resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.inheritedReader');

			$this->assertInstanceOf(CachedReader::class, $resolved);
			$this->assertInstanceOf(AnnotationReader::class, $resolved->getReader());
			$this->assertSame($resolved, resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.inheritedReader'));
		}

		public function testCacheMemoryDriver() {
			Config::set(AnnotationsServiceProvider::PACKAGE_NAME . '.cache', 'memory');

			$resolved = resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.cache');

			$this->assertInstanceOf(MemoryCache::class, $resolved);
		}

		public function testCachePhpDriver() {
			Config::set(AnnotationsServiceProvider::PACKAGE_NAME . '.cache', 'php');
			Config::set(AnnotationsServiceProvider::PACKAGE_NAME . '.caches', [
				'php' => [
					'driver'    => 'php',
					'directory' => '/tmp/asd/'
				]
			]);

			/** @var PhpCache $resolved */
			$resolved = resolve(AnnotationsServiceProvider::PACKAGE_NAME . '.cache');

			$this->assertInstanceOf(PhpCache::class, $resolved);
			$this->assertEquals('/tmp/asd/', $resolved->getCachePath());
		}

		public function testAnnotationCacheClearedWithCache() {

			/** @var AnnotationCache|MockObject $cacheMock */
			$cacheMock = $this->mockAppSingleton(AnnotationsServiceProvider::PACKAGE_NAME . '.cache', AnnotationCache::class);
			$cacheMock->expects($this->once())
				->method('clear')
				->willReturnSelf();

			$this->assertEquals(0, Artisan::call('cache:clear'));
		}
	}