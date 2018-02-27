<?php

	namespace ItsMiegerAnnotationsTests\Cases\Facades;


	use ItsMieger\Annotations\Facades\InheritedAnnotationReader;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;
	use ItsMieger\Annotations\Reader\InheritedAnnotationReader as InheritedAnnotationReaderContract;
	use ItsMiegerAnnotationsTests\Cases\TestCase;

	class InheritedAnnotationReaderTest extends TestCase
	{
		public function testAncestorCall() {
			// mock ancestor
			$mock = $this->mockAppSingleton(AnnotationsServiceProvider::PACKAGE_NAME . '.inheritedReader', InheritedAnnotationReaderContract::class);
			$mock->expects($this->once())
				->method('getClassAnnotations')
				->with('clsName');

			InheritedAnnotationReader::getClassAnnotations('clsName');
		}
	}