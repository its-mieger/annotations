<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 27.02.18
	 * Time: 19:19
	 */

	namespace ItsMieger\Annotations\Facades;


	use Illuminate\Support\Facades\Facade;
	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;

	/**
	 * Static interface to the annotation manager
	 * @method AnnotationsManager register($className, $name) static Registers a new annotation class
	 * @method bool isRegistered($shortName) static Checks if a short name is registered
	 * @method string|null getClass($shortName) static Gets the class for the registered short name
	 * @method bool isIgnored($name) static Checks if an annotation should be ignored
	 */
	class Annotations extends Facade
	{

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 */
		protected static function getFacadeAccessor() {
			return AnnotationsServiceProvider::PACKAGE_NAME . '.manager';
		}

	}