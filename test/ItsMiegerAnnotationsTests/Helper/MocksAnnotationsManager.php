<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 16:03
	 */

	namespace ItsMiegerAnnotationsTests\Helper;


	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Contracts\Annotation;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;

	trait MocksAnnotationsManager
	{
		protected function mockManager($annotations = []) {
			$mock = $this->getMockBuilder(AnnotationsManager::class)->getMock();

			app()->singleton(AnnotationsServiceProvider::PACKAGE_NAME . '.manager', function () use ($mock) {
				return $mock;
			});

			$mapClass = [];
			$mapHas = [];
			foreach($annotations as $key => $value) {
				if (!($value instanceof Annotation)) {
					$ann = $this->getMockBuilder(Annotation::class)->getMock();
				}
				else {
					$ann = $value;
				}

				$mapClass[] = [$key, get_class($ann)];
				$mapHas[] = [$key, true];

			}

			$mock->method('getClass')->willReturnMap($mapClass);
			$mock->method('isRegistered')->willReturnMap($mapHas);

			return $mock;
		}
	}