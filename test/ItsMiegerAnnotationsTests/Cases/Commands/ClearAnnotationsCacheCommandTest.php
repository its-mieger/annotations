<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 27.02.18
	 * Time: 15:32
	 */

	namespace ItsMiegerAnnotationsTests\Cases\Commands;


	use ItsMieger\Annotations\Commands\ClearAnnotationCacheCommand;
	use ItsMieger\Annotations\Contracts\AnnotationCache;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;
	use ItsMiegerAnnotationsTests\Cases\TestCase;
	use PHPUnit\Framework\MockObject\MockObject;

	class ClearAnnotationsCacheCommandTest extends TestCase
	{


		public function testClearCache() {

			/** @var AnnotationCache|MockObject $cacheMock */
			$cacheMock = $this->mockAppSingleton( AnnotationsServiceProvider::PACKAGE_NAME . '.cache', AnnotationCache::class);
			$cacheMock->expects($this->once())
				->method('clear')
				->willReturnSelf();

			$this->assertCommandSuccess(ClearAnnotationCacheCommand::class);
		}

	}