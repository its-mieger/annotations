<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 13:57
	 */

	namespace ItsMieger\Annotations\Exceptions;


	use Throwable;

	/**
	 * Thrown if an error occurred on parsing annotations
	 * @package ItsMieger\Annotations\Exceptions
	 */
	class AnnotationParseException extends \Exception
	{
		protected $docBlock;

		/**
		 * Creates a new instance
		 * @param string $docBlock
		 * @param string $message
		 * @param int $code
		 * @param Throwable|null $previous
		 */
		public function __construct($docBlock, $message = "", $code = 0, Throwable $previous = null) {
			$this->docBlock = $docBlock;

			if (!$message)
				$message = 'Error parsing annotations doc block';

			parent::__construct($message, $code, $previous);
		}

		/**
		 * @return string
		 */
		public function getDocBlock(): string {
			return $this->docBlock;
		}


	}