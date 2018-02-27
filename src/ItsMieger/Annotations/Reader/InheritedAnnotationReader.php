<?php

	namespace ItsMieger\Annotations\Reader;


	use ItsMieger\Annotations\Contracts\Annotation;

	/**
	 * Reads annotations and inherited or implemented annotations from a class
	 *
	 * @package ItsMieger\Annotations\Reader
	 */
	class InheritedAnnotationReader extends AnnotationReader
	{
		/**
		 * @inheritDoc
		 */
		public function getClassAnnotations($cls, $filter = []) {

			$annotations = [];

			// collect annotations of class and parents
			$cls = $this->reflect($cls);
			$classes = $this->getSelfAndAllParents($cls);
			for ($i = count($classes) - 1; $i >= 0; --$i) {
				$annotations = array_merge($annotations, parent::getClassAnnotations($classes[$i], $filter));
			}

			// append interface annotations
			foreach($cls->getInterfaces() as $curr) {
				$annotations = array_merge($annotations, parent::getClassAnnotations($curr, $filter));
			}

			return $this->aggregateLastByName($annotations);
		}

		/**
		 * @inheritDoc
		 */
		public function getMethodAnnotations($cls, $method, $filter = []) {

			$annotations = [];

			// collect annotations of class and parents
			$cls     = $this->reflect($cls);
			$classes = $this->getSelfAndAllParents($cls);
			for ($i = count($classes) - 1; $i >= 0; --$i) {
				if ($classes[$i]->hasMethod($method))
					$annotations = array_merge($annotations, parent::getMethodAnnotations($classes[$i], $method, $filter));
			}

			// append interface annotations
			foreach ($cls->getInterfaces() as $curr) {
				if ($curr->hasMethod($method))
					$annotations = array_merge($annotations, parent::getMethodAnnotations($curr, $method, $filter));
			}

			return $this->aggregateLastByName($annotations);
		}

		/**
		 * @inheritDoc
		 */
		public function getPropertyAnnotations($cls, $property, $filter = []) {
			$annotations = [];

			// collect annotations of class and parents
			$cls     = $this->reflect($cls);
			$classes = $this->getSelfAndAllParents($cls);
			for ($i = count($classes) - 1; $i >= 0; --$i) {
				if ($classes[$i]->hasProperty($property))
					$annotations = array_merge($annotations, parent::getPropertyAnnotations($classes[$i], $property, $filter));
			}

			return $this->aggregateLastByName($annotations);
		}


		/**
		 * Gets the class and all parents of the given class
		 * @param \ReflectionClass $cls
		 * @return \ReflectionClass[] The parent classes
		 */
		protected function getSelfAndAllParents(\ReflectionClass $cls) {
			$ret = [$cls];

			$parentCls = $cls->getParentClass();
			while ($parentCls) {
				$ret[] = $parentCls;
				$parentCls = $parentCls->getParentClass();
			}

			return $ret;
		}

		/**
		 * Aggregates annotations thus only the last annotation with the same name is returned
		 * @param Annotation[] $annotations The annotations
		 * @return Annotation[] The aggregated annotations
		 */
		protected function aggregateLastByName(array $annotations) {
			$ret = [];

			foreach($annotations as $curr) {
				$ret[$curr->getAnnotationType()] = $curr;
			}

			return $ret;
		}

	}