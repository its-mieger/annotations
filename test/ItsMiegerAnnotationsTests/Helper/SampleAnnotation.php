<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 16:32
	 */

	namespace ItsMiegerAnnotationsTests\Helper;


	use ItsMieger\Annotations\Contracts\Annotation;

	class SampleAnnotation implements Annotation
	{
		/**
		 * @inheritDoc
		 */
		public static function fromJson(array $data): Annotation {
			return new self($data['parameters'] ?? []);
		}


		public $parameters = [];

		/**
		 * @inheritDoc
		 */
		public function getAnnotationType(): string {
			return 'SampleAnnotationName';
		}

		/**
		 * @inheritDoc
		 */
		public function __construct(array $parameters) {
			$this->parameters = $parameters;
		}

		/**
		 * @inheritDoc
		 */
		function jsonSerialize() {
			return [
				'parameters' => $this->parameters,
			];
		}

		/**
		 * @inheritDoc
		 */
		public function getParameters(): array {
			return $this->parameters;
		}


	}