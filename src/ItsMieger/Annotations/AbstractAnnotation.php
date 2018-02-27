<?php

	namespace ItsMieger\Annotations;


	use ItsMieger\Annotations\Contracts\Annotation;

	/**
	 * Basis for implementing custom annotations
	 * @package ItsMieger\Annotations
	 */
	abstract class AbstractAnnotation implements Annotation
	{
		protected $parameters;

		/**
		 * @inheritDoc
		 */
		public function __construct(array $parameters) {
			$this->parameters = $parameters;
		}


		/**
		 * @inheritDoc
		 */
		public static function fromJson(array $data): Annotation {
			return new static($data);
		}

		/**
		 * @inheritDoc
		 */
		function jsonSerialize() {
			return $this->parameters;
		}


		/**
		 * @inheritDoc
		 */
		public function getAnnotationType(): string {
			return get_class($this);
		}

	}