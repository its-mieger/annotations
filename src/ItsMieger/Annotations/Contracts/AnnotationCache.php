<?php

	namespace ItsMieger\Annotations\Contracts;


	/**
	 * Interface for annotation caches
	 * @package ItsMieger\Annotations\Contracts
	 */
	interface AnnotationCache
	{

		/**
		 * Writes annotations to the cache
		 * @param string $readerKey Key for the reader
		 * @param string $cls The class name
		 * @param string $memberType The member type
		 * @param string $memberName The member name
		 * @param Annotation[] $annotations The annotations to write
		 * @return $this
		 */
		public function put($readerKey, $cls, $memberType, $memberName, array $annotations);


		/**
		 * Gets the annotations from the cache
		 * @param string $readerKey Key for the reader
		 * @param string $cls The class name
		 * @param string $memberType The member type
		 * @param string $memberName The member name
		 * @return Annotation[]|null The annotations or null if not in cache
		 */
		public function get($readerKey, $cls, $memberType, $memberName);

		/**
		 * Clears the cache
		 * @return $this
		 */
		public function clear();
	}