<?php

	namespace ItsMieger\Annotations\Reader;


	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Contracts\AnnotationCache;
	use ItsMieger\Annotations\Contracts\AnnotationReader;

	/**
	 * Implements a cached annotations reader
	 *
	 * @package ItsMieger\Annotations\Reader
	 */
	class CachedReader implements AnnotationReader
	{
		use FiltersAnnotations;

		/**
		 * @var AnnotationCache
		 */
		protected $cache;

		/**
		 * @var AnnotationsManager
		 */
		protected $manager;

		/**
		 * @var AnnotationReader
		 */
		protected $reader;

		/**
		 * @var string
		 */
		protected $readerKey;

		/**
		 * Creates a new instance
		 * @param AnnotationCache $cache The cache
		 * @param AnnotationsManager $manager The annotations manager
		 * @param AnnotationReader $reader The annotations reader
		 */
		public function __construct(AnnotationCache $cache, AnnotationsManager $manager, AnnotationReader $reader) {
			$this->cache   = $cache;
			$this->manager = $manager;
			$this->reader  = $reader;
		}

		/**
		 * Gets the internal reader
		 * @return AnnotationReader The internal reader
		 */
		public function getReader() {
			return $this->reader;
		}

		/**
		 * @inheritDoc
		 */
		public function getClassAnnotations($cls, $filter = []) {
			$annotations = $this->cache->get($this->getReaderKey(), $cls, 'class', 'cls');

			if ($annotations === null) {
				$annotations = $this->reader->getClassAnnotations($cls);
				$this->cache->put($this->getReaderKey(), $cls, 'class', 'cls', $annotations);
			}

			return $this->filterAnnotations($annotations, $filter);
		}

		/**
		 * @inheritDoc
		 */
		public function getClassAnnotation($cls, $annotationName) {
			$ret = $this->getClassAnnotations($cls, [$annotationName]);

			return array_pop($ret);
		}

		/**
		 * @inheritDoc
		 */
		public function getMethodAnnotations($cls, $method, $filter = []) {
			$annotations = $this->cache->get($this->getReaderKey(), $cls, 'method', $method);

			if ($annotations === null) {
				$annotations = $this->reader->getMethodAnnotations($cls, $method);
				$this->cache->put($this->getReaderKey(), $cls, 'method', $method, $annotations);
			}

			return $this->filterAnnotations($annotations, $filter);
		}

		/**
		 * @inheritDoc
		 */
		public function getMethodAnnotation($cls, $method, $annotationName) {
			$ret = $this->getMethodAnnotations($cls, $method, [$annotationName]);

			return array_pop($ret);
		}

		/**
		 * @inheritDoc
		 */
		public function getPropertyAnnotations($cls, $property, $filter = []) {
			$annotations = $this->cache->get($this->getReaderKey(), $cls, 'property', $property);

			if ($annotations === null) {
				$annotations = $this->reader->getPropertyAnnotations($cls, $property);
				$this->cache->put($this->getReaderKey(), $cls, 'property', $property, $annotations);
			}

			return $this->filterAnnotations($annotations, $filter);
		}

		/**
		 * @inheritDoc
		 */
		public function getPropertyAnnotation($cls, $property, $annotationName) {
			$ret = $this->getPropertyAnnotations($cls, $property, [$annotationName]);

			return array_pop($ret);
		}

		/**
		 * Gets the key for the internal reader
		 * @return string The key for the internal reader
		 */
		protected function getReaderKey() {
			return $this->readerKey ?: ($this->readerKey = get_class($this->reader));
		}

		/**
		 * @inheritDoc
		 */
		protected function manager() {
			return $this->manager;
		}
	}