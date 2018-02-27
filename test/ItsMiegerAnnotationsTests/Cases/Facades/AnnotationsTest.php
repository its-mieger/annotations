<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 27.02.18
	 * Time: 19:20
	 */

	namespace ItsMiegerAnnotationsTests\Cases\Facades;


	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Facades\Annotations;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;
	use ItsMiegerAnnotationsTests\Cases\TestCase;

	class AnnotationsTest extends TestCase
	{
		public function testAncestorCall() {
			// mock ancestor
			$mock = $this->mockAppSingleton(AnnotationsServiceProvider::PACKAGE_NAME . '.manager', AnnotationsManager::class);
			$mock->expects($this->once())
				->method('register')
				->with('clsName', 'shortName');

			Annotations::register('clsName', 'shortName');
		}
	}