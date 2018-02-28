<?php

	namespace ItsMieger\Annotations\Provider;


	use Illuminate\Contracts\Events\Dispatcher;
	use Illuminate\Support\ServiceProvider;
	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Cache\ClearsAnnotationCache;
	use ItsMieger\Annotations\Cache\CreatesAnnotationCache;
	use ItsMieger\Annotations\Commands\ClearAnnotationCacheCommand;
	use ItsMieger\Annotations\Parser\AnnotationParser;
	use ItsMieger\Annotations\Reader\AnnotationReader;
	use ItsMieger\Annotations\Reader\CachedReader;
	use ItsMieger\Annotations\Reader\InheritedAnnotationReader;

	class AnnotationsServiceProvider extends ServiceProvider
	{
		use CreatesAnnotationCache;
		use ClearsAnnotationCache;

		const PACKAGE_NAME = 'annotations';

		protected $packageRoot = __DIR__ . '/../../../..';

		/**
		 * Indicates if loading of the provider is deferred.
		 *
		 * @var bool
		 */
		protected $defer = true;

		/**
		 * Bootstrap the application services.
		 *
		 * @return void
		 */
		public function boot(Dispatcher $events) {

			// register commands
			if ($this->app->runningInConsole()) {
				$this->commands([
					'command.' . self::PACKAGE_NAME . '.clear'
				]);
			}

			// listen for cache clear events
			$events->listen('cache:clearing', function () {
				$this->clearAnnotationCache();
			});
		}

		/**
		 * Register the service provider.
		 *
		 * @return void
		 */
		public function register() {
			$this->mergeConfigFrom($this->packageRoot . '/config/config.php', self::PACKAGE_NAME);

			// register manager
			$this->app->singleton(self::PACKAGE_NAME . '.manager', function() {
				return new AnnotationsManager();
			});

			// register parser
			$this->app->singleton(self::PACKAGE_NAME . '.parser', function () {
				return new AnnotationParser();
			});

			// register cache
			$this->app->singleton(self::PACKAGE_NAME . '.cache', function () {
				$cache = config(self::PACKAGE_NAME . '.cache');

				$configuredCaches = config(self::PACKAGE_NAME . '.caches', []) ?: [];
				if (!array_key_exists($cache, $configuredCaches))
					throw new \RuntimeException('Annotation cache "' . $cache . '" is not configured');

				return $this->createCache($configuredCaches[$cache]);
			});

			// register default reader
			$this->app->singleton(self::PACKAGE_NAME . '.reader', function () {
				$manager = $this->app->make(self::PACKAGE_NAME . '.manager');

				return new CachedReader(
					$this->app->make(self::PACKAGE_NAME . '.cache'),
					$manager,
					new AnnotationReader(
						$this->app->make(self::PACKAGE_NAME . '.parser'),
						$manager
					)
				);
			});

			// register inherited reader
			$this->app->singleton(self::PACKAGE_NAME . '.inheritedReader', function () {
				$manager = $this->app->make(self::PACKAGE_NAME . '.manager');

				return new CachedReader(
					$this->app->make(self::PACKAGE_NAME . '.cache'),
					$manager,
					new InheritedAnnotationReader(
						$this->app->make(self::PACKAGE_NAME . '.parser'),
						$manager
					)
				);
			});

			// register clear cache command
			$this->app->singleton('command.' . self::PACKAGE_NAME . '.clear', function () {
				return new ClearAnnotationCacheCommand();
			});
		}

		/**
		 * Get the services provided by the provider.
		 *
		 * @return array
		 */
		public function provides() {
			return [
				self::PACKAGE_NAME . '.manager',
				self::PACKAGE_NAME . '.parser',
				self::PACKAGE_NAME . '.cache',
				self::PACKAGE_NAME . '.reader',
				self::PACKAGE_NAME . '.inheritedReader',
				'command.' . self::PACKAGE_NAME . '.clear',
			];
		}


		/**
		 * Registers a new cache driver resolver
		 * @param string $driver The driver name
		 * @param callable $fn The resolver function
		 * @return $this
		 */
		public function registerCacheResolver($driver, callable $fn) {
			$this->customCacheResolvers[$driver] = $fn;

			return $this;
		}
	}