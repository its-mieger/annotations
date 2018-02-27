<?php

	namespace ItsMieger\Annotations\Cache;


	use ItsMieger\Annotations\Contracts\AnnotationCache;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;

	trait ClearsAnnotationCache
	{

		protected function clearAnnotationCache() {
			/** @var AnnotationCache $cache */
			$cache = app(AnnotationsServiceProvider::PACKAGE_NAME . '.cache');

			// clear
			$cache->clear();
		}

	}