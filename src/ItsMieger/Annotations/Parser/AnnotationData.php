<?php

	namespace ItsMieger\Annotations\Parser;


	/**
	 * Parsed annotation data
	 * @package ItsMieger\Annotations\Parser
	 */
	class AnnotationData
	{
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var array
		 */
		protected $parameters;

		/**
		 * Creates a new instance
		 * @param string $name The name
		 * @param array $parameters The parameters
		 */
		public function __construct($name, array $parameters = []) {
			$this->name       = $name;
			$this->parameters = $parameters;
		}

		/**
		 * Gets the annotation name
		 * @return string The annotation name
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * Gets the annotation parameters
		 * @return array The annotation parameters
		 */
		public function getParameters(): array {
			return $this->parameters;
		}



	}