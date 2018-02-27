<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 27.02.18
	 * Time: 14:48
	 */

	namespace ItsMiegerAnnotationsTests\Cases\Facades;


	use ItsMieger\Annotations\Contracts\AnnotationReader as AnnotationReaderContract;
	use ItsMieger\Annotations\Facades\AnnotationReader;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;
	use ItsMiegerAnnotationsTests\Cases\TestCase;

	class AnnotationReaderTest extends TestCase
	{
		public function testAncestorCall() {
			// mock ancestor
			$mock = $this->mockAppSingleton(AnnotationsServiceProvider::PACKAGE_NAME . '.reader', AnnotationReaderContract::class);
			$mock->expects($this->once())
				->method('getClassAnnotations')
				->with('clsName');

			AnnotationReader::getClassAnnotations('clsName');
		}
	}