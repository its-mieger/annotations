<?php

	namespace ItsMiegerAnnotationsTests\Helper;


	use ItsMieger\Annotations\AbstractAnnotation;

	class AbstractAnnotationTestClass extends AbstractAnnotation
	{
		protected $parameters;


		public function getParameters() {
			return $this->parameters;
		}


	}