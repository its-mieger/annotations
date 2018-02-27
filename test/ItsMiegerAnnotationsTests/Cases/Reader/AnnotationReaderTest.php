<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 26.02.18
	 * Time: 15:42
	 */

	namespace ItsMiegerAnnotationsTests\Cases\Reader;


	use ItsMieger\Annotations\Parser\AnnotationData;
	use ItsMieger\Annotations\Reader\AnnotationReader;
	use ItsMiegerAnnotationsTests\Cases\TestCase;
	use ItsMiegerAnnotationsTests\Helper\MocksAnnotationsManager;
	use ItsMiegerAnnotationsTests\Helper\MocksParser;
	use ItsMiegerAnnotationsTests\Helper\SampleAnnotation;
	use Nette\PhpGenerator\ClassType;
	use Nette\PhpGenerator\Helpers;

	class AnnotationReaderTest extends TestCase
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

		public function testGetClassAnnotationsIgnored() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			// ignore anno2
			$manager->method('isIgnored')
				->willReturnMap([
					['anno1', false],
					['anno2', true]
				]);

			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getClassAnnotations($cls->getName());

			$this->assertCount(1, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
		}

		public function testGetClassAnnotationsString() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getClassAnnotations($cls->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetClassAnnotationsInstance() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);
			$instance = $this->createInstance($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getClassAnnotations($instance);

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetClassAnnotationsReflectionClass() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);
			$instance = $this->createInstance($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getClassAnnotations(new \ReflectionClass($instance));

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetClassAnnotationsFilteredByName() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getClassAnnotations($cls->getName(), ['anno2']);

			$this->assertCount(1, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
		}

		public function testGetClassAnnotationsFilteredByClass() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getClassAnnotations($cls->getName(), [SampleAnnotation::class]);

			$this->assertCount(1, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
		}

		public function testGetClassAnnotationsWithParameters() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1', ['a' => 7, 'b' => 5]);
			$anno2 = new AnnotationData('anno2', ['a' => 8, 'b' => 9]);

			$manager = $this->mockManager(['anno1' => new SampleAnnotation([]), 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getClassAnnotations($cls->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
			$this->assertEquals(['a' => 7, 'b' => 5], $annotations[0]->parameters);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[1]);
			$this->assertEquals(['a' => 8, 'b' => 9], $annotations[1]->parameters);
		}

		public function testGetClassAnnotationByName() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotation= $reader->getClassAnnotation($cls->getName(), 'anno2');

			$this->assertInstanceOf(SampleAnnotation::class, $annotation);
		}

		public function testGetClassAnnotationByClass() {
			$cls = new ClassType($this->clsName());

			$cls->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotation= $reader->getClassAnnotation($cls->getName(), SampleAnnotation::class);

			$this->assertInstanceOf(SampleAnnotation::class, $annotation);
		}
		
		public function testGetMethodAnnotationsString() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');
			;

			$this->define($cls);

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$parser = $this->mockParser([$anno1, $anno2], str_replace("\n", "\n\t", Helpers::formatDocComment($method->getComment() . "\n")));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getMethodAnnotations($cls->getName(), $method->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetMethodAnnotationsInstance() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');

			$this->define($cls);
			$instance = $this->createInstance($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$parser = $this->mockParser([$anno1, $anno2], str_replace("\n", "\n\t", Helpers::formatDocComment($method->getComment() . "\n")));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getMethodAnnotations($instance, $method->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetMethodAnnotationsReflectionClass() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');

			$this->define($cls);
			$instance = $this->createInstance($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$parser = $this->mockParser([$anno1, $anno2], str_replace("\n", "\n\t", Helpers::formatDocComment($method->getComment() . "\n")));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getMethodAnnotations(new \ReflectionClass($instance), $method->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetMethodAnnotationsFilteredByName() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], str_replace("\n", "\n\t", Helpers::formatDocComment($method->getComment() . "\n")));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getMethodAnnotations($cls->getName(), $method->getName(), ['anno2']);

			$this->assertCount(1, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
		}

		public function testGetMethodAnnotationsFilteredByClass() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], str_replace("\n", "\n\t", Helpers::formatDocComment($method->getComment() . "\n")));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getMethodAnnotations($cls->getName(), $method->getName(), [SampleAnnotation::class]);

			$this->assertCount(1, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
		}

		public function testGetMethodAnnotationsWithParameters() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1', ['a' => 7, 'b' => 5]);
			$anno2 = new AnnotationData('anno2', ['a' => 8, 'b' => 9]);

			$manager = $this->mockManager(['anno1' => new SampleAnnotation([]), 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($cls->getComment() . "\n"));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getMethodAnnotations($cls->getName(), $method->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
			$this->assertEquals(['a' => 7, 'b' => 5], $annotations[0]->parameters);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[1]);
			$this->assertEquals(['a' => 8, 'b' => 9], $annotations[1]->parameters);
		}

		public function testGetMethodAnnotationByName() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], str_replace("\n", "\n\t", Helpers::formatDocComment($method->getComment() . "\n")));

			$reader = new AnnotationReader($parser, $manager);

			$annotation= $reader->getMethodAnnotation($cls->getName(), $method->getName(), 'anno2');

			$this->assertInstanceOf(SampleAnnotation::class, $annotation);
		}

		public function testGetMethodAnnotationByClass() {
			$cls = new ClassType($this->clsName());

			$method = $cls->addMethod('myMethod')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], str_replace("\n", "\n\t", Helpers::formatDocComment($method->getComment() . "\n")));

			$reader = new AnnotationReader($parser, $manager);

			$annotation= $reader->getMethodAnnotation($cls->getName(), $method->getName(), SampleAnnotation::class);

			$this->assertInstanceOf(SampleAnnotation::class, $annotation);
		}
		
		public function testGetPropertyAnnotationsString() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');
			;

			$this->define($cls);

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getPropertyAnnotations($cls->getName(), $property->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetPropertyAnnotationsInstance() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');

			$this->define($cls);
			$instance = $this->createInstance($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getPropertyAnnotations($instance, $property->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetPropertyAnnotationsReflectionClass() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');

			$this->define($cls);
			$instance = $this->createInstance($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => 'anno2']);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getPropertyAnnotations(new \ReflectionClass($instance), $property->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf($manager->getClass('anno1'), $annotations[0]);
			$this->assertInstanceOf($manager->getClass('anno2'), $annotations[1]);
		}

		public function testGetPropertyAnnotationsFilteredByName() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getPropertyAnnotations($cls->getName(), $property->getName(), ['anno2']);

			$this->assertCount(1, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
		}

		public function testGetPropertyAnnotationsFilteredByClass() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getPropertyAnnotations($cls->getName(), $property->getName(), [SampleAnnotation::class]);

			$this->assertCount(1, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
		}

		public function testGetPropertyAnnotationByName() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotation= $reader->getPropertyAnnotation($cls->getName(), $property->getName(), 'anno2');

			$this->assertInstanceOf(SampleAnnotation::class, $annotation);
		}

		public function testGetPropertyAnnotationByClass() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1');
			$anno2 = new AnnotationData('anno2');

			$manager = $this->mockManager(['anno1' => 'anno1', 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotation= $reader->getPropertyAnnotation($cls->getName(), $property->getName(), SampleAnnotation::class);

			$this->assertInstanceOf(SampleAnnotation::class, $annotation);
		}

		public function testGetPropertyAnnotationsWithParameters() {
			$cls = new ClassType($this->clsName());

			$property = $cls->addProperty('myProperty')
				->addComment('@Anno1');

			$this->define($cls);


			$anno1 = new AnnotationData('anno1', ['a' => 7, 'b' => 5]);
			$anno2 = new AnnotationData('anno2', ['a' => 8, 'b' => 9]);

			$manager = $this->mockManager(['anno1' => new SampleAnnotation([]), 'anno2' => new SampleAnnotation([])]);

			$parser = $this->mockParser([$anno1, $anno2], Helpers::formatDocComment($property->getComment()));

			$reader = new AnnotationReader($parser, $manager);

			$annotations = $reader->getPropertyAnnotations($cls->getName(), $property->getName());

			$this->assertCount(2, $annotations);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[0]);
			$this->assertEquals(['a' => 7, 'b' => 5], $annotations[0]->parameters);
			$this->assertInstanceOf(SampleAnnotation::class, $annotations[1]);
			$this->assertEquals(['a' => 8, 'b' => 9], $annotations[1]->parameters);
		}
	}