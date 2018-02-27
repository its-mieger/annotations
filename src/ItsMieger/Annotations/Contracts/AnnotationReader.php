<?php
	namespace ItsMieger\Annotations\Contracts;


	interface AnnotationReader
	{
		/**
		 * Gets the class annotations for the given class
		 * @param object|string|\ReflectionClass $cls The class
		 * @param string[] $filter Annotation name or class filters
		 * @return Annotation[] The annotations
		 */
		public function getClassAnnotations($cls, $filter = []);

		/**
		 * Gets the class annotation with the specified name
		 * @param object|string|\ReflectionClass $cls The class
		 * @param string $annotationName The annotation name or annotation class name
		 * @return Annotation|null The annotation or null
		 */
		public function getClassAnnotation($cls, $annotationName);

		/**
		 * Gets the method annotations for the given class method
		 * @param object|string|\ReflectionClass $cls The class
		 * @param string $method The method name
		 * @param string[] $filter Annotation name or class filters
		 * @return Annotation[] The annotations
		 */
		public function getMethodAnnotations($cls, $method, $filter = []);

		/**
		 * Gets the method annotation with the given name
		 * @param object|string|\ReflectionClass $cls The class
		 * @param string $method The method name
		 * @param string $annotationName The annotation name or annotation class name
		 * @return Annotation|null The annotation or null
		 */
		public function getMethodAnnotation($cls, $method, $annotationName);


		/**
		 * Gets the property annotations for the given class property
		 * @param object|string|\ReflectionClass $cls The class
		 * @param string $property The property name
		 * @param string[] $filter Annotation name or class filters
		 * @return Annotation[] The annotations
		 */
		public function getPropertyAnnotations($cls, $property, $filter = []);

		/**
		 * Gets the property annotation with the given name
		 * @param object|string|\ReflectionClass $cls The class
		 * @param string $property The property name
		 * @param string $annotationName The annotation name or annotation class name
		 * @return Annotation|null The annotation or null
		 */
		public function getPropertyAnnotation($cls, $property, $annotationName);
	}