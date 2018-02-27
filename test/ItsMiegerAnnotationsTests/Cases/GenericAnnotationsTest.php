<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 15:06
	 */

	namespace ItsMiegerAnnotationsTests\Cases;


	use ItsMieger\Annotations\GenericAnnotation;

	class GenericAnnotationsTest extends TestCase
	{

		public function testWithName() {
			$instance = GenericAnnotation::withName('my-name', ['a' => 7, 'b' => 8]);

			$this->assertEquals('generic:my-name', $instance->getAnnotationType());
			$this->assertEquals(['a' => 7, 'b' => 8], $instance->getParameters());
		}


		public function testSerializeUnserialize() {
			$instance = GenericAnnotation::withName('my-name', ['a' => 7, 'b' => 8]);

			$this->assertEquals($instance, GenericAnnotation::fromJson($instance->jsonSerialize()));
		}

	}