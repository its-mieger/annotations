<?php
	namespace ItsMieger\Annotations\Facades;


	use Illuminate\Support\Facades\Facade;
	use ItsMieger\Annotations\Contracts\Annotation;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;

	/**
	 * Static interface to the default annotation reader
	 * @method Annotation[] getClassAnnotations($cls, $filter = []) static Gets the class annotations for the given class
	 * @method Annotation getClassAnnotation($cls, $annotationName) static Gets the class annotation with the specified name
	 * @method Annotation[] getMethodAnnotations($cls, $method, $filter = []) static Gets the method annotations for the given class method
	 * @method Annotation getMethodAnnotation($cls, $method, $annotationName) static Gets the method annotations with the given name
	 * @method Annotation[] getPropertyAnnotations($cls, $property, $filter = []) static Gets the property annotations for the given class property
	 * @method Annotation getPropertyAnnotation($cls, $property, $filter = []) static Gets the property annotation with the given name
	 */
	class AnnotationReader extends Facade
	{

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 */
		protected static function getFacadeAccessor() {
			return AnnotationsServiceProvider::PACKAGE_NAME . '.reader';
		}

	}