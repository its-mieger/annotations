<?php

	namespace ItsMiegerAnnotationsTests\Cases;

	use Illuminate\Console\Command;
	use Illuminate\Events\EventServiceProvider;
	use Illuminate\Support\Facades\Artisan;
	use ItsMieger\Annotations\Provider\AnnotationsServiceProvider;

	abstract class TestCase extends \Orchestra\Testbench\TestCase {

		protected $lastOutput = null;

		protected function getLastCommandOutput() {
			return $this->lastOutput;
		}

		protected function callCommand($cmdClass, $parameters = []) {

			/** @var Command $cmd */
			$cmd = app()->make($cmdClass);

			$ret = Artisan::call($cmd->getName(), $parameters);

			$this->lastOutput = Artisan::output();

			return $ret;
		}

		protected function assertCommandSuccess($command, $parameters = []) {
			$this->assertEquals(0, $this->callCommand($command, $parameters), 'Execution of command "' . $command . '" should succeed. Output: ' . $this->getLastCommandOutput());
		}

		/**
		 * Mocks an instance in the application service container
		 * @param string $instance The instance to mock
		 * @param string|null $mockedClass The class to use for creating a mock object. Null to use same as $instance
		 * @return \PHPUnit\Framework\MockObject\MockObject
		 */
		protected function mockAppSingleton($instance, $mockedClass = null) {

			if (!$mockedClass)
				$mockedClass = $instance;

			$mock = $this->getMockBuilder($mockedClass)->disableOriginalConstructor()->getMock();
			app()->singleton($instance, function () use ($mock) {
				return $mock;
			});

			return $mock;
		}

		/**
		 * Load package service provider
		 * @param  \Illuminate\Foundation\Application $app
		 * @return array
		 */
		protected function getPackageProviders($app) {
			return [
				AnnotationsServiceProvider::class,
				EventServiceProvider::class,
			];
		}
	}