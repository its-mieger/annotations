<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 17:04
	 */

	namespace ItsMiegerAnnotationsTests\Cases\Reader;



	use ItsMieger\Annotations\AnnotationsManager;
	use ItsMieger\Annotations\Parser\AnnotationParser;
	use ItsMieger\Annotations\Reader\InheritedAnnotationReader;
	use ItsMiegerAnnotationsTests\Cases\TestCase;
	use ItsMiegerAnnotationsTests\Helper\MocksAnnotationsManager;
	use ItsMiegerAnnotationsTests\Helper\MocksParser;
	use ItsMiegerAnnotationsTests\Helper\SampleAnnotation;
	use Nette\PhpGenerator\ClassType;


	class InheritedAnnotationsReaderTest extends TestCase
	{
		use MocksParser;
		use MocksAnnotationsManager;

		protected static $classId = 0;


		protected function clsName($type = 'class') {
			return preg_replace('/.*?\\\\([^\\\\]+)$/', '$1', __CLASS__) . 'Test' . ucfirst($type) . (++self::$classId) . '_' . uniqid();
		}

		protected function define(ClassType ...$cls) {
			foreach ($cls as $currCls) {
				eval((string)$currCls);
			}
		}

		protected function createInstance(ClassType $cls, ...$arguments) {

			$name = $cls->getName();

			return new $name(...$arguments);
		}

		protected function classInstance(ClassType $cls, ...$arguments) {
			eval((string)$cls);

			$clsName = $cls->getName();

			return new $clsName(...$arguments);
		}

		protected function assertAnnotations($expected, $actual) {
			$this->assertCount(count($expected), $actual);

			foreach($expected as $name => $parameters) {
				$found = false;
				foreach ($actual as $curr) {
					if ($curr->getAnnotationType() == $name) {
						$found = true;

						$this->assertEquals($parameters, $curr->getParameters(), 'Parameters for @' . $name . ' do not match:');
					}
				}

				if (!$found)
					$this->fail('Missing annotation @' . $name . ' in result');
				
			}
		}

		public function testGetClassAnnotations() {

			$ifA = new ClassType($this->clsName('Interface_A_'));
			$ifA->setType('interface')
				->addComment('@AnnotationA')
				->addComment('@AnnotationDAOverride(fromA)')
			;


			$ifB = new ClassType($this->clsName('Interface_B_'));
			$ifB->setType('interface')
				->addComment('@AnnotationB')
			;


			$ifC = new ClassType($this->clsName('Interface_C_'));
			$ifC->setType('interface')
				->addComment('@AnnotationC');


			$ifDA = new ClassType($this->clsName('Interface_DA_'));
			$ifDA->setType('interface')
				->addExtend($ifA->getName())
				->addComment('@AnnotationDAOverride(fromDA)')
				->addComment('@AnnotationDA')
			;

			$ifE = new ClassType($this->clsName('Interface_E_'));
			$ifE->setType('interface');

			$clsParent = new ClassType($this->clsName());
			$clsParent
				->addImplement($ifC->getName())
				->addComment('@AnnotationParent')
				->addComment('@AnnotationParentOverride(fromParent)')
			;



			$cls = new ClassType($this->clsName());
			$cls
				->addComment('@AnnotationClass')
				->addComment('@AnnotationParentOverride(fromClass)')
				->addComment('@AnnotationDAOverride(fromClass)')
				->addImplement($ifB->getName())
				->addExtend($clsParent->getName())
				->addImplement($ifDA->getName())
				->addImplement($ifE->getName());

			$this->define($ifA, $ifB, $ifC, $ifDA, $ifE, $clsParent, $cls);

			$reader = new InheritedAnnotationReader(new AnnotationParser(), new AnnotationsManager());

			$annotations = $reader->getClassAnnotations($cls->getName());

			$this->assertAnnotations([
				'generic:AnnotationA' => [],
				'generic:AnnotationB' => [],
				'generic:AnnotationC' => [],
				'generic:AnnotationDA' => [],
				'generic:AnnotationDAOverride' => ['fromA'],
				'generic:AnnotationParent' => [],
				'generic:AnnotationParentOverride' => ['fromClass'],
				'generic:AnnotationClass' => [],
			], $annotations);
		}

		public function testGetClassAnnotationsFilteredByName() {

			$ifA = new ClassType($this->clsName('Interface_A_'));
			$ifA->setType('interface')
				->addComment('@AnnotationA')
				->addComment('@AnnotationDAOverride(fromA)')
			;


			$ifB = new ClassType($this->clsName('Interface_B_'));
			$ifB->setType('interface')
				->addComment('@AnnotationB')
			;


			$ifC = new ClassType($this->clsName('Interface_C_'));
			$ifC->setType('interface')
				->addComment('@AnnotationC');


			$ifDA = new ClassType($this->clsName('Interface_DA_'));
			$ifDA->setType('interface')
				->addExtend($ifA->getName())
				->addComment('@AnnotationDAOverride(fromDA)')
				->addComment('@AnnotationDA')
			;

			$ifE = new ClassType($this->clsName('Interface_E_'));
			$ifE->setType('interface');

			$clsParent = new ClassType($this->clsName());
			$clsParent
				->addImplement($ifC->getName())
				->addComment('@AnnotationParent')
				->addComment('@AnnotationParentOverride(fromParent)')
			;



			$cls = new ClassType($this->clsName());
			$cls
				->addComment('@AnnotationClass')
				->addComment('@AnnotationParentOverride(fromClass)')
				->addComment('@AnnotationDAOverride(fromClass)')
				->addImplement($ifB->getName())
				->addExtend($clsParent->getName())
				->addImplement($ifDA->getName())
				->addImplement($ifE->getName());

			$this->define($ifA, $ifB, $ifC, $ifDA, $ifE, $clsParent, $cls);

			$manager = $this->mockManager(['AnnotationParentOverride' => new SampleAnnotation([])]);

			$reader = new InheritedAnnotationReader(new AnnotationParser(), $manager);

			$annotations = $reader->getClassAnnotations($cls->getName(), [SampleAnnotation::class]);


			$this->assertAnnotations([
				'SampleAnnotationName' => ['fromClass'],
			], $annotations);
		}

		public function testGetMethodAnnotations() {

			$ifA = new ClassType($this->clsName('Interface_A_'));
			$ifA->setType('interface')
				->addMethod('myMethod')
				->addComment('@AnnotationA')
				->addComment('@AnnotationDAOverride(fromA)');


			$ifB = new ClassType($this->clsName('Interface_B_'));
			$ifB->setType('interface')
				->addMethod('myMethod')
				->addComment('@AnnotationB');


			$ifC = new ClassType($this->clsName('Interface_C_'));
			$ifC->setType('interface')
				->addMethod('myMethod')
				->addComment('@AnnotationC');


			$ifDA = new ClassType($this->clsName('Interface_DA_'));
			$ifDA->setType('interface')
				->addExtend($ifA->getName())
				->addMethod('myMethod')
				->addComment('@AnnotationDAOverride(fromDA)')
				->addComment('@AnnotationDA');

			$ifE = new ClassType($this->clsName('Interface_E_'));
			$ifE->setType('interface')
				->addMethod('myMethod')
			;

			$clsParent = new ClassType($this->clsName());
			$clsParent
				->addImplement($ifC->getName())
				->addMethod('myMethod')
				->addComment('@AnnotationParent')
				->addComment('@AnnotationParentOverride(fromParent)');


			$cls = new ClassType($this->clsName());
			$cls
				->addImplement($ifB->getName())
				->addImplement($ifE->getName())
				->addExtend($clsParent->getName())
				->addImplement($ifDA->getName())
				->addMethod('myMethod')
				->addComment('@AnnotationClass')
				->addComment('@AnnotationDAOverride(fromClass)')
				->addComment('@AnnotationParentOverride(fromClass)');

			$this->define($ifA, $ifB, $ifC, $ifDA, $ifE, $clsParent, $cls);

			$reader = new InheritedAnnotationReader(new AnnotationParser(), new AnnotationsManager());

			$annotations = $reader->getMethodAnnotations($cls->getName(), 'myMethod');

			$this->assertAnnotations([
				'generic:AnnotationA'              => [],
				'generic:AnnotationB'              => [],
				'generic:AnnotationC'              => [],
				'generic:AnnotationDA'             => [],
				'generic:AnnotationDAOverride'     => ['fromA'],
				'generic:AnnotationParent'         => [],
				'generic:AnnotationParentOverride' => ['fromClass'],
				'generic:AnnotationClass'          => [],
			], $annotations);
		}

		public function testGetMethodAnnotationsFiltered() {

			$ifA = new ClassType($this->clsName('Interface_A_'));
			$ifA->setType('interface')
				->addMethod('myMethod')
				->addComment('@AnnotationA')
				->addComment('@AnnotationDAOverride(fromA)');


			$ifB = new ClassType($this->clsName('Interface_B_'));
			$ifB->setType('interface')
				->addMethod('myMethod')
				->addComment('@AnnotationB');


			$ifC = new ClassType($this->clsName('Interface_C_'));
			$ifC->setType('interface')
				->addMethod('myMethod')
				->addComment('@AnnotationC');


			$ifDA = new ClassType($this->clsName('Interface_DA_'));
			$ifDA->setType('interface')
				->addExtend($ifA->getName())
				->addMethod('myMethod')
				->addComment('@AnnotationDAOverride(fromDA)')
				->addComment('@AnnotationDA');

			$ifE = new ClassType($this->clsName('Interface_E_'));
			$ifE->setType('interface')
				->addMethod('myMethod')
			;

			$clsParent = new ClassType($this->clsName());
			$clsParent
				->addImplement($ifC->getName())
				->addMethod('myMethod')
				->addComment('@AnnotationParent')
				->addComment('@AnnotationParentOverride(fromParent)');


			$cls = new ClassType($this->clsName());
			$cls
				->addImplement($ifB->getName())
				->addImplement($ifE->getName())
				->addExtend($clsParent->getName())
				->addImplement($ifDA->getName())
				->addMethod('myMethod')
				->addComment('@AnnotationClass')
				->addComment('@AnnotationDAOverride(fromClass)')
				->addComment('@AnnotationParentOverride(fromClass)');

			$this->define($ifA, $ifB, $ifC, $ifDA, $ifE, $clsParent, $cls);

			$manager = $this->mockManager(['AnnotationParentOverride' => new SampleAnnotation([])]);

			$reader = new InheritedAnnotationReader(new AnnotationParser(), $manager);

			$annotations = $reader->getMethodAnnotations($cls->getName(), 'myMethod', [SampleAnnotation::class]);


			$this->assertAnnotations([
				'SampleAnnotationName' => ['fromClass'],
			], $annotations);
		}

		public function testGetPropertyAnnotations() {

			$clsParent = new ClassType($this->clsName());
			$clsParent
				->addProperty('myProperty')
				->addComment('@AnnotationParent')
				->addComment('@AnnotationParentOverride(fromParent)');


			$cls = new ClassType($this->clsName());
			$cls
				->addExtend($clsParent->getName())
				->addProperty('myProperty')
				->addComment('@AnnotationClass')
				->addComment('@AnnotationParentOverride(fromClass)');

			$this->define($clsParent, $cls);

			$reader = new InheritedAnnotationReader(new AnnotationParser(), new AnnotationsManager());

			$annotations = $reader->getPropertyAnnotations($cls->getName(), 'myProperty');

			$this->assertAnnotations([
				'generic:AnnotationParent'         => [],
				'generic:AnnotationParentOverride' => ['fromClass'],
				'generic:AnnotationClass'          => [],
			], $annotations);
		}

		public function testGetPropertyAnnotationsFiltered() {

			$clsParent = new ClassType($this->clsName());
			$clsParent
				->addProperty('myProperty')
				->addComment('@AnnotationParent')
				->addComment('@AnnotationParentOverride(fromParent)');


			$cls = new ClassType($this->clsName());
			$cls
				->addExtend($clsParent->getName())
				->addProperty('myProperty')
				->addComment('@AnnotationClass')
				->addComment('@AnnotationParentOverride(fromClass)');

			$this->define($clsParent, $cls);

			$manager = $this->mockManager(['AnnotationParentOverride' => new SampleAnnotation([])]);

			$reader = new InheritedAnnotationReader(new AnnotationParser(), $manager);

			$annotations = $reader->getPropertyAnnotations($cls->getName(), 'myProperty', [SampleAnnotation::class]);

			$this->assertAnnotations([
				'SampleAnnotationName' => ['fromClass'],
			], $annotations);
		}
	}