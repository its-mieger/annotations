<?php

	namespace ItsMiegerAnnotationsTests\Cases;


	use ItsMieger\Annotations\AnnotationsManager;

	class AnnotationsManagerTest extends TestCase
	{

		public function testRegister() {
			$mng = new AnnotationsManager();

			// register
			$this->assertSame($mng, $mng->register('/test/cls/name1', 'name1'));
			$this->assertSame($mng, $mng->register('/test/cls/name2', 'name2'));

			// check registered
			$this->assertTrue($mng->isRegistered('name1'));
			$this->assertTrue($mng->isRegistered('name2'));
			$this->assertFalse($mng->isRegistered('name3'));

			// get class
			$this->assertEquals('/test/cls/name1', $mng->getClass('name1'));
			$this->assertEquals('/test/cls/name2', $mng->getClass('name2'));
			$this->assertEquals(null, $mng->getClass('name3'));
		}

		public function testIsIgnored() {
			$mng = new AnnotationsManager();

			$this->assertSame($mng, $mng->register('/test/cls/var', 'var'));

			$this->assertTrue($mng->isIgnored('return'));
			$this->assertFalse($mng->isIgnored('nameX'));

			// registered annotations should not be ignored
			$this->assertFalse($mng->isIgnored('var'));
		}

	}