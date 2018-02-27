<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 14:55
	 */

	namespace ItsMieger\Annotations\Contracts;


	interface GenericAnnotation extends Annotation
	{
		/**
		 * Creates a new instance with the specified name
		 * @param string $name The name
		 * @param array $parameters The parameters
		 * @return static
		 */
		public static function withName($name, array $parameters);

		/**
		 * Gets the annotation parameters
		 * @return array The parameters
		 */
		public function getParameters() : array;
	}