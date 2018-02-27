<?php

	namespace ItsMieger\Annotations\Cache;


	use ItsMieger\Annotations\Contracts\AnnotationCache;

	/**
	 * Implements in-memory annotation cache. This should only used for testing.
	 *
	 * @package ItsMieger\Annotations\Cache
	 */
	class MemoryCache implements AnnotationCache
	{
		protected $data = [];

		/**
		 * @inheritdoc
		 */
		public function put($readerKey, $cls, $memberType, $memberName, array $annotations) {
			$this->data[$readerKey][$cls][$memberType][$memberName] = $annotations;

			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get($readerKey, $cls, $memberType, $memberName) {
			return $this->data[$readerKey][$cls][$memberType][$memberName] ?? null;
		}

		/**
		 * @inheritdoc
		 */
		public function clear() {
			$this->data = [];

			return $this;
		}
	}