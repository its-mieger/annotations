<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 27.02.18
	 * Time: 09:52
	 */

	namespace ItsMieger\Annotations\Reader;


	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Contracts\Annotation;

	trait FiltersAnnotations
	{
		/**
		 * Gets the annotations manager instance
		 * @return AnnotationsManager The annotation manager instance
		 */
		protected abstract function manager();

		/**
		 * Filters the annotations
		 * @param Annotation[] $annotations The annotations to filter
		 * @param string[] $filters The filters. Empty to filter nothing
		 * @return Annotation[] The filtered annotations
		 */
		protected function filterAnnotations(array $annotations, $filters) {
			if (!$filters)
				return $annotations;

			if (!is_array($filters))
				$filters = [$filters];

			// map short names to class names
			foreach ($filters as &$currFilter) {
				$currFilter = $this->manager()->getClass($currFilter) ?: $currFilter;
			}


			$ret = [];
			foreach ($annotations as $curr) {
				if (in_array(get_class($curr), $filters))
					$ret[] = $curr;
			}

			return $ret;
		}
	}