<?php


	namespace ItsMieger\Annotations;

	use ItsMieger\Annotations\Contracts\Annotation;
	use ItsMieger\Annotations\Contracts\GenericAnnotation;

	/**
	 * Represents an annotation without parameters
	 * @package ItsMieger\Annotations
	 */
	class GenericMarkerAnnotation implements GenericAnnotation
	{
		protected $name;

		/**
		 * @inheritDoc
		 */
		public static function fromJson(array $data): Annotation {
			return static::withName($data['name'] ?? '', []);
		}

		/**
		 * @inheritdoc
		 */
		public static function withName($name, array $parameters) {
			$ret = new static($parameters);

			$ret->name = (string)$name;

			return $ret;
		}

		/**
		 * @inheritdoc
		 */
		public function getAnnotationType(): string {
			return 'generic:' . $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function __construct(array $parameters) {

		}

		/**
		 * @inheritDoc
		 */
		public function getParameters(): array {
			return [];
		}

		/**
		 * @inheritDoc
		 */
		function jsonSerialize() {
			return [
				'name' => $this->name,
			];
		}


	}