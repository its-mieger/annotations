<?php

	namespace ItsMieger\Annotations\Reader;


	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Contracts\Annotation;
	use ItsMieger\Annotations\Contracts\AnnotationReader as AnnotationReaderContract;
	use ItsMieger\Annotations\GenericAnnotation;
	use ItsMieger\Annotations\GenericMarkerAnnotation;
	use ItsMieger\Annotations\Parser\AnnotationData;
	use ItsMieger\Annotations\Parser\AnnotationParser;

	/**
	 * Reads annotations from a class
	 *
	 * @package ItsMieger\Annotations\Reader
	 */
	class AnnotationReader implements AnnotationReaderContract
	{
		use FiltersAnnotations;

		/**
		 * @var AnnotationParser
		 */
		protected $parser;

		/**
		 * @var AnnotationsManager
		 */
		protected $manager;


		public function __construct(AnnotationParser $parser, AnnotationsManager $manager) {
			$this->parser  = $parser;
			$this->manager = $manager;
		}

		/**
		 * @inheritdoc
		 */
		public function getClassAnnotations($cls, $filter = []) {

			// reflect
			$cls = $this->reflect($cls);

			// parse annotations
			$annotations = $this->getDocCommentAnnotations($cls->getDocComment());

			// return filtered
			return $this->filterAnnotations($annotations, $filter);
		}

		/**
		 * @inheritdoc
		 */
		public function getClassAnnotation($cls, $annotationName) {
			$ret = $this->getClassAnnotations($cls, [$annotationName]);

			return array_pop($ret);
		}

		/**
		 * @inheritdoc
		 */
		public function getMethodAnnotations($cls, $method, $filter = []) {

			// reflect
			$cls = $this->reflect([$cls, $method], 'method');

			// parse annotations
			$annotations = $this->getDocCommentAnnotations($cls->getDocComment());

			// return filtered
			return $this->filterAnnotations($annotations, $filter);
		}

		/**
		 * @inheritdoc
		 */
		public function getMethodAnnotation($cls, $method, $annotationName) {
			$ret = $this->getMethodAnnotations($cls, $method, [$annotationName]);

			return array_pop($ret);
		}


		/**
		 * @inheritdoc
		 */
		public function getPropertyAnnotations($cls, $property, $filter = []) {
			// reflect
			$cls = $this->reflect([$cls, $property], 'property');

			// parse annotations
			$annotations = $this->getDocCommentAnnotations($cls->getDocComment());

			// return filtered
			return $this->filterAnnotations($annotations, $filter);
		}

		/**
		 * @inheritdoc
		 */
		public function getPropertyAnnotation($cls, $property, $annotationName) {
			$ret = $this->getPropertyAnnotations($cls, $property, [$annotationName]);

			return array_pop($ret);
		}

		/**
		 * @inheritDoc
		 */
		protected function manager() {
			return $this->manager;
		}


		/**
		 * Reflects the specified member of the given class
		 * @param object|\ReflectionClass|array $cls The class to reflect
		 * @param string $member The member name (class, method, property)
		 * @return \ReflectionClass|\ReflectionMethod|\ReflectionProperty The reflected member
		 */
		protected function reflect($cls, $member = 'class') {

			// expand
			if (is_array($cls))
				[$cls, $memberName] = $cls;

			if (!($cls instanceof \ReflectionClass))
				$cls = new \ReflectionClass($cls);

			switch ($member) {
				case 'method':
					if (empty($memberName))
						throw new \RuntimeException('Missing member name for reflection');

					return $cls->getMethod($memberName);
				case 'property':
					if (empty($memberName))
						throw new \RuntimeException('Missing member name for reflection');

					return $cls->getProperty($memberName);
				case 'class':
					return $cls;

				default:
					throw new \RuntimeException('Unknown reflection member "' . $member . '"');

			}
		}

		/**
		 * Gets the annotations in the given doc comment
		 * @param string $docComment The doc comment
		 * @return Annotation[] The annotations
		 */
		protected function getDocCommentAnnotations($docComment) {

			if (!$docComment)
				return [];

			$annotationsData = $this->parser->parseDocComment($docComment);

			$ret = [];
			foreach($annotationsData as $curr) {
				if ($currAnnotation = $this->createAnnotationInstance($curr))
					$ret[] = $currAnnotation;
			}

			return $ret;
		}

		/**
		 * Creates an annotation instance for the given annotation data
		 * @param AnnotationData $data The annotation data
		 * @return Annotation The annotation instance
		 */
		protected function createAnnotationInstance(AnnotationData $data) {
			$params = $data->getParameters();
			$name = $data->getName();

			// check for ignored annotation
			if ($this->manager->isIgnored($name))
				return null;

			if (!class_exists($name)) {
				if ($cls = $this->manager->getClass($name))
					return new $cls($data->getParameters());
			}
			else {
				return new $name($data->getParameters());
			}

			// fallback to generic
			if ($params)
				return GenericAnnotation::withName($name, $params);
			else
				return GenericMarkerAnnotation::withName($name, $params);
		}
	}