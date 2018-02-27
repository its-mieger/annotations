<?php

	namespace ItsMiegerAnnotationsTests\Cases;


	use ItsMieger\Annotations\GenericMarkerAnnotation;

	class GenericMarkerAnnotationsTest extends TestCase
	{

		public function testWithName() {
			$instance = GenericMarkerAnnotation::withName('my-name', ['a' => 7, 'b' => 8]);

			$this->assertEquals('generic:my-name', $instance->getAnnotationType());
			$this->assertEquals([], $instance->getParameters());
		}

		public function testSerializeUnserialize() {
			$instance = GenericMarkerAnnotation::withName('my-name', ['a' => 7, 'b' => 8]);

			$this->assertEquals($instance, GenericMarkerAnnotation::fromJson($instance->jsonSerialize()));
		}
	}