<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 10:06
	 */

	namespace ItsMieger\Annotations\Contracts;


	interface Annotation extends \JsonSerializable
	{
		/**
		 * Restores the annotation from unserialized data
		 * @param array $data The unserialized data
		 * @return Annotation The annotation instance
		 */
		public static function fromJson(array $data) : Annotation;

		/**
		 * Gets the annotation type name. This is typically the fully qualified class name.
		 * @return string The annotation type name. This is typically the fully qualified class name.
		 */
		public function getAnnotationType(): string;

		/**
		 * Creates a new instance
		 * @param array $parameters The parameters for the annotation
		 */
		public function __construct(array $parameters);


	}