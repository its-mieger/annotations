<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 10:08
	 */

	namespace ItsMieger\Annotations\Parser;


	use ItsMieger\Annotations\Exceptions\AnnotationParseException;

	/**
	 * Parses annotations from a doc comment
	 * @package ItsMieger\Annotations\Parser
	 */
	class AnnotationParser
	{

		/**
		 * Parses the specified doc comment for annotations
		 * @param string $docComment The doc comment
		 * @return AnnotationData[] The parsed annotations
		 * @throws AnnotationParseException
		 */
		public function parseDocComment($docComment) {
			$annotations = [];

			// trim doc comment
			$docComment = $this->trimDocBlock($docComment);


			$escaped                  = false;
			$quoteBlock               = null;
			$expressionStack          = [];
			$inlineStack              = [];
			$annotationSection        = null;
			$annotationName           = null;
			$annotationParams         = null;
			$annotationExtendedSyntax = false;

			// ends the currently parsed annotation and puts it on return stack
			$endAnnotation = function() use (&$annotations, &$annotationName, &$annotationParams, &$annotationExtendedSyntax, &$annotationSection) {
				$name = preg_replace('/[\x00-\x1F\x7F]/u', '', $annotationName);
				$params = array_map(function($curr) {
					return preg_replace('/[\x00-\x1F\x7F]/u', '', $curr);
				}, $annotationParams);

				$annotations[] = new AnnotationData($name, $params);

				$annotationName = null;
				$annotationParams = [];
				$annotationExtendedSyntax = false;
				$annotationSection = null;
			};

			// appends the value to the last annotation parameter
			$appendLastParameter = function($char) use (&$annotationParams) {
				$keys = array_keys($annotationParams);

				$annotationParams[end($keys)] = ($annotationParams[end($keys)] ?? '') . $char;
			};


			foreach(preg_split('/(?<!^)(?!$)/u', $docComment) as $currChar) {

				// escaped char
				if ($escaped) {
					if (!in_array($currChar, ['\\', '"', '\'', '@', '(', ')', '[', ']', '{', '}']))
						$currChar = '\\' . $currChar;

					switch($annotationSection) {
						case 'NAME':
							$annotationName .= $currChar;
							break;
						case 'PARAM':
							$appendLastParameter($currChar);
							break;
					}

					$escaped = false;
					continue;
				}

				// inline annotations
				if (!$annotationSection) {
					if ($currChar == '{') {
						$inlineStack[] = $currChar;
					}
					elseif ($currChar == '}' && !empty($inlineStack)) {
						array_pop($inlineStack);
					}
				}
				if (!empty($inlineStack))
					continue;


				switch($currChar) {
					case '\\':
						$escaped = true;
						break;

					/** @noinspection PhpMissingBreakStatementInspection */
					case '@':
						if (!$annotationSection) {
							$annotationSection        = 'NAME';
							$annotationName           = '';
							$annotationParams         = [];
							$annotationExtendedSyntax = false;
							break;
						}
						else {
							// fall through to annotation
						}

					default:
						switch($annotationSection) {
							case 'NAME':
								switch($currChar) {
									/** @noinspection PhpMissingBreakStatementInspection */
									case '(';
										$expressionStack[] = $currChar;
										$annotationExtendedSyntax = true;
									case ' ';
									case "\t";
										$annotationSection = 'PARAM';
										break;

									case '@':
										$endAnnotation();
										$annotationSection = 'NAME';
										break;

									case "\n";
										$endAnnotation();
										break;

									default:
										$annotationName .= $currChar;
								}
								break;

							case 'PARAM':
								if ($quoteBlock) {
									// end of quote block
									if ($quoteBlock == $currChar) {
										$quoteBlock = null;
									}
									else {
										// append to last parameter
										$appendLastParameter($currChar);
									}
									break;
								}

								if ($annotationExtendedSyntax) {
									// extended syntax

									switch ($currChar) {
										case '"';
										case "'";
											// start of quote block
											$quoteBlock = $currChar;
											break;

										case '(';
										case '{';
										case '[';
											// start of expression
											$expressionStack[] = $currChar;
											break;

										case ')';
										case ']';
										case '}';
											// end of expression
											if (end($expressionStack) != [')' => '(', ']' => '[', '}' => '{'][$currChar])
												throw new AnnotationParseException($docComment,'Unexpected "' . $currChar . '" in annotation @' . $annotationName);
											else
												array_pop($expressionStack);

											// end annotation on last closing brace
											if ($currChar == ')' && empty($expressionStack))
												$endAnnotation();
											break;

										case ',':
											// for extended syntax, this starts a new parameter

											if ($expressionStack != ['('])
												throw new AnnotationParseException($docComment, 'Unexpected "' . $currChar . '" in annotation @' . $annotationName);

											$annotationParams[] = '';
											break;

										case '=':
											// named parameters

											$parameterKeys = array_keys($annotationParams);
											if (!is_numeric(end($parameterKeys)) || !end($annotationParams))
												throw new AnnotationParseException($docComment, 'Unexpected "' . $currChar . '" in annotation @' . $annotationName);

											$key                    = array_pop($annotationParams);
											$annotationParams[$key] = '';
											break;

										case ' ':
										case "\t";
										case "\n";
											// ignore these white space characters
											break;

										default:

											// append to last parameter
											$appendLastParameter($currChar);
									}
								}
								else {
									// simple syntax

									switch($currChar) {
										case "\n";
											$endAnnotation();
											break;

										case ' ';
										case "\t";
											if (end($annotationParams))
												$annotationParams[] = '';

											break;

										default:

											// append to last parameter
											$appendLastParameter($currChar);
									}
								}


						}

				}
			}

			if ($annotationSection) {
				if (!empty($quoteBlock))
					throw new AnnotationParseException($docComment,'String literal not ended properly in annotation' . $annotationName);
				if (!empty($expressionStack))
					throw new AnnotationParseException($docComment,'Expression not ended properly in annotation' . $annotationName);

				$endAnnotation();
			}


			return $annotations;
		}


		protected function trimDocBlock($docComment) {
			$docComment = trim(substr($docComment, 3, -2));

			// replace asterisks on begin of lines
			$docComment = preg_replace('/^[\s]*\\*+[\s]*/m', '', $docComment);

			return $docComment;
		}
	}