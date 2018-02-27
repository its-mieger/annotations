<?php

	namespace ItsMiegerAnnotationsTests\Cases;

	use ItsMiegerAnnotationsTests\Helper\AbstractAnnotationTestClass;

	class AbstractAnnotationTest extends TestCase
	{

		public function testFromJson() {
			$params = ['a' => 7, 'b' => 8];

			/** @var AbstractAnnotationTestClass $annotation */
			$annotation = AbstractAnnotationTestClass::fromJson($params);

			$this->assertEquals($params, $annotation->getParameters());
		}

		public function testGetAnnotationType() {

			/** @var AbstractAnnotationTestClass $annotation */
			$annotation = new AbstractAnnotationTestClass([]);

			$this->assertEquals(get_class($annotation), $annotation->getAnnotationType());
		}

		public function testJsonSerialize() {

			$params = ['a' => 7, 'b' => 8];

			/** @var AbstractAnnotationTestClass $annotation */
			$annotation = new AbstractAnnotationTestClass($params);

			$this->assertEquals($params, $annotation->jsonSerialize());

		}

	}