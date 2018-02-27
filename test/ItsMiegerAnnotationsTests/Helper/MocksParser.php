<?php

	namespace ItsMiegerAnnotationsTests\Helper;


	use ItsMieger\Annotations\Parser\AnnotationParser;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;

	trait MocksParser
	{
		protected function mockParser($annotations = [], $docComment = null) {
			$mock = $this->getMockBuilder(AnnotationParser::class)->getMock();

			app()->singleton(AnnotationsServiceProvider::PACKAGE_NAME . '.parser', function() use ($mock) {
				return $mock;
			});


			$method = $mock
				->method('parseDocComment')
				->willReturn($annotations);

			if ($docComment)
				$method->with(trim($docComment));

			return $mock;
		}
	}