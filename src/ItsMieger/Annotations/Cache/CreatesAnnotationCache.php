<?php

	namespace ItsMieger\Annotations\Cache;


	trait CreatesAnnotationCache
	{
		protected $customCacheResolvers = [];

		protected function createCache($config) {
			if (!is_array($config))
				$config = ['driver' => $config];

			switch ($config['driver']) {
				case 'memory':
					return $this->createCacheMemory();
				case 'php':
					return $this->createCachePhp($config);
				default:
					if (!isset($this->customCacheResolvers[$config['driver']]))
						throw new \Exception('Annotation cache driver "' . $config['driver'] . '" not found');

					return call_user_func($this->customCacheResolvers[$config['driver']], $config);
			}
		}

		/**
		 * Creates a new memory cache instance
		 * @return MemoryCache The memory cache instance
		 */
		protected function createCacheMemory() {
			return new MemoryCache();
		}

		/**
		 * Creates a new PHP cache instance
		 * @param array $config The configuration array
		 * @return PhpCache
		 */
		protected function createCachePhp($config) {
			return new PhpCache($config['directory'] ?? sys_get_temp_dir());
		}
	}