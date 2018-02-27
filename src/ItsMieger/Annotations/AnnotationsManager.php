<?php

	namespace ItsMieger\Annotations;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;


	/**
	 * Manages the recognized annotations
	 */
	class AnnotationsManager
	{
		protected $annotations = [];

		protected $ignored = null;

		/**
		 * Registers a new annotation class
		 * @param string $className The class
		 * @param null $name The short name as returned by Annotation::getName
		 * @return $this
		 */
		public function register($className, $name) {
			$this->annotations[$name ?? $className] = $className;

			return $this;
		}

		/**
		 * Checks if a short name is registered
		 * @param string $shortName The short name
		 * @return bool True if registered. Else false.
		 */
		public function isRegistered($shortName) : bool {
			return array_key_exists($shortName, $this->annotations);
		}

		/**
		 * Gets the class for the registered short name
		 * @param string $shortName The short name
		 * @return string|null The annotation class name
		 */
		public function getClass($shortName) {
			return $this->annotations[$shortName] ?? null;
		}

		/**
		 * Checks if an annotation should be ignored
		 * @param string $name The annotation name
		 * @return bool True if should be ignored. Else false.
		 */
		public function isIgnored($name) : bool {
			if ($this->ignored === null)
				$this->ignored = array_fill_keys(config(AnnotationsServiceProvider::PACKAGE_NAME . '.ignore', []) ?: [], true);

			return !array_key_exists($name, $this->annotations) && array_key_exists($name, $this->ignored);
		}
	}