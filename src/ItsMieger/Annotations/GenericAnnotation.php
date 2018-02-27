<?php

	namespace ItsMieger\Annotations;

	use ItsMieger\Annotations\Contracts\Annotation;
	use ItsMieger\Annotations\Contracts\GenericAnnotation as GenericAnnotationContract;

	class GenericAnnotation implements GenericAnnotationContract
	{
		protected $name;
		protected $parameters;

		/**
		 * @inheritDoc
		 */
		public static function fromJson(array $data): Annotation {
			return static::withName($data['name'] ?? '', $data['parameters'] ?? []);
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
			$this->parameters = $parameters;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameters(): array {
			return $this->parameters;
		}

		/**
		 * @inheritDoc
		 */
		function jsonSerialize() {
			return [
				'name'       => $this->name,
				'parameters' => $this->parameters,
			];
		}


	}