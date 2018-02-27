<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 18:47
	 */

	namespace ItsMieger\Annotations\Cache;


	use ItsMieger\Annotations\Contracts\Annotation;
	use ItsMieger\Annotations\Contracts\AnnotationCache;

	/**
	 * Implements an annotation cache using PHP files as storage
	 *
	 * @package ItsMieger\Annotations\Cache
	 */
	class PhpCache implements AnnotationCache
	{
		/**
		 * @var string The cache path
		 */
		protected $cachePath;

		/**
		 * @var Annotation[][][][][]
		 */
		protected $data = [];

		/**
		 * Creates a new instance
		 * @param string $cachePath The cache path
		 */
		public function __construct($cachePath) {
			$this->cachePath = $cachePath;

			// create cache path
			try {
				if (!file_exists($this->cachePath))
					mkdir($this->cachePath, 0777, true);
			}
			catch (\Throwable $ex) {
				report($ex);
			}
		}

		/**
		 * Gets the cache path
		 * @return string The cache path
		 */
		public function getCachePath() {
			return $this->cachePath;
		}

		/**
		 * @inheritdoc
		 */
		public function put($readerKey, $cls, $memberType, $memberName, array $annotations) {
			$this->data[$readerKey][$cls][$memberType][$memberName] = $annotations;

			$this->persist($readerKey, $cls);

			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get($readerKey, $cls, $memberType, $memberName) {
			if (!array_key_exists($readerKey, $this->data) ||  !array_key_exists($cls, $this->data[$readerKey]))
				$this->load($readerKey, $cls);

			return $this->data[$readerKey][$cls][$memberType][$memberName] ?? null;
		}

		/**
		 * @inheritdoc
		 */
		public function clear() {
			$files = glob($this->cachePath . DIRECTORY_SEPARATOR . '*');

			foreach ($files as $currFile) {
				if (is_file($currFile))
					unlink($currFile);
			}

			$this->data = [];

			return $this;
		}

		/**
		 * Loads the cache data for the file system from cache
		 * @param string $cls The class name
		 */
		protected function load($readerKey, $cls) {
			$path = $this->getCachedPath($readerKey, $cls);

			try {
				if (file_exists($path))
					$this->data[$readerKey][$cls] = include $path;
				else
					$this->data[$readerKey][$cls] = [];
			}
			catch (\Throwable $ex) {
				report($ex);
			}
		}

		/**
		 * Persists the content for the given class and reader key
		 * @param string $readerKey The reader key
		 * @param string $cls The class name
		 */
		protected function persist($readerKey, $cls) {

			$markup = '<?php return array(';
			foreach ($this->data[$readerKey][$cls] as $memberType => &$members) {
				$markup .= var_export($memberType, true) . '=>array(';
				foreach ($members as $memberName => &$annotations) {
					$markup .= var_export($memberName, true) . '=>array(';
					foreach ($annotations as &$curr) {
						$markup .= get_class($curr) . '::fromJson(' . var_export($curr->jsonSerialize(), true) . '),';
					}
					$markup .= '),';
				}
				$markup .= '),';
			}
			$markup .= ');';

			try {
				file_put_contents($this->getCachedPath($readerKey, $cls), $markup);
			}
			catch (\Throwable $ex) {
				report($ex);
			}
		}

		/**
		 * Gets the cache file path for the given class name and reader key
		 * @param string $readerKey The reader key
		 * @param string $cls The class name
		 * @return string The cache file name
		 */
		protected function getCachedPath($readerKey, $cls) {

			return $this->cachePath . DIRECTORY_SEPARATOR . sha1($readerKey . ':' . $cls) . '.php';
		}
	}