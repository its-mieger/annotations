<?php

	namespace ItsMiegerAnnotationsTests\Cases\Reader;


	use ItsMieger\Annotations\Cache\MemoryCache;
	use ItsMieger\Annotations\Contracts\Annotation;
	use ItsMieger\Annotations\Contracts\AnnotationReader;
	use ItsMieger\Annotations\Reader\CachedReader;
	use ItsMiegerAnnotationsTests\Cases\TestCase;
	use ItsMiegerAnnotationsTests\Helper\MocksAnnotationsManager;
	use ItsMiegerAnnotationsTests\Helper\SampleAnnotation;
	use PHPUnit\Framework\MockObject\MockObject;

	class CachedAnnotationsReaderTest extends TestCase
	{
		use MocksAnnotationsManager;


		public function testGetClassAnnotations() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getClassAnnotations')
				->with('className', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getClassAnnotations('className'));
			$this->assertEquals($annotations, $cachedReader->getClassAnnotations('className'));
		}

		public function testGetClassAnnotations_Reflected() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			$rc = new \ReflectionClass($this);

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getClassAnnotations')
				->with($rc, [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getClassAnnotations($rc));
			$this->assertEquals($annotations, $cachedReader->getClassAnnotations($rc));
		}

		public function testGetClassAnnotations_Instance() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getClassAnnotations')
				->with($this, [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getClassAnnotations($this));
			$this->assertEquals($annotations, $cachedReader->getClassAnnotations($this));
		}

		public function testGetClassAnnotationsFiltered() {

			$annotations = [
				$this->getMockBuilder(Annotation::class)->getMock(),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getClassAnnotations')
				->with('className', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals([$annotations[1]], $cachedReader->getClassAnnotations('className', [SampleAnnotation::class]));
			$this->assertEquals([$annotations[1]], $cachedReader->getClassAnnotations('className', [SampleAnnotation::class]));
			$this->assertEquals($annotations, $cachedReader->getClassAnnotations('className'));
		}

		public function testGetClassAnnotation() {

			$annotations = [
				$this->getMockBuilder(Annotation::class)->getMock(),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getClassAnnotations')
				->with('className', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations[1], $cachedReader->getClassAnnotation('className', SampleAnnotation::class));
			$this->assertEquals($annotations[0], $cachedReader->getClassAnnotation('className', get_class($annotations[0])));
		}
		
		public function testGetMethodAnnotations() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getMethodAnnotations')
				->with('className', 'methodName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getMethodAnnotations('className', 'methodName'));
			$this->assertEquals($annotations, $cachedReader->getMethodAnnotations('className', 'methodName'));
		}

		public function testGetMethodAnnotations_Reflected() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			$rc = new \ReflectionClass($this);

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getMethodAnnotations')
				->with($rc, 'methodName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getMethodAnnotations($rc, 'methodName'));
			$this->assertEquals($annotations, $cachedReader->getMethodAnnotations($rc, 'methodName'));
		}

		public function testGetMethodAnnotations_Instance() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getMethodAnnotations')
				->with($this, 'methodName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getMethodAnnotations($this, 'methodName'));
			$this->assertEquals($annotations, $cachedReader->getMethodAnnotations($this, 'methodName'));
		}

		public function testGetMethodAnnotationsFiltered() {

			$annotations = [
				$this->getMockBuilder(Annotation::class)->getMock(),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getMethodAnnotations')
				->with('className', 'methodName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals([$annotations[1]], $cachedReader->getMethodAnnotations('className', 'methodName', [SampleAnnotation::class]));
			$this->assertEquals([$annotations[1]], $cachedReader->getMethodAnnotations('className', 'methodName', [SampleAnnotation::class]));
			$this->assertEquals($annotations, $cachedReader->getMethodAnnotations('className', 'methodName'));
		}

		public function testGetMethodAnnotation() {

			$annotations = [
				$this->getMockBuilder(Annotation::class)->getMock(),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getMethodAnnotations')
				->with('className', 'methodName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations[1], $cachedReader->getMethodAnnotation('className', 'methodName', SampleAnnotation::class));
			$this->assertEquals($annotations[0], $cachedReader->getMethodAnnotation('className', 'methodName', get_class($annotations[0])));
		}
		
		public function testGetPropertyAnnotations() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getPropertyAnnotations')
				->with('className', 'propertyName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getPropertyAnnotations('className', 'propertyName'));
			$this->assertEquals($annotations, $cachedReader->getPropertyAnnotations('className', 'propertyName'));
		}

		public function testGetPropertyAnnotations_Reflected() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			$rc = new \ReflectionClass($this);

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getPropertyAnnotations')
				->with($rc, 'propertyName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getPropertyAnnotations($rc, 'propertyName'));
			$this->assertEquals($annotations, $cachedReader->getPropertyAnnotations($rc, 'propertyName'));
		}

		public function testGetPropertyAnnotations_Instance() {

			$annotations = [
				new SampleAnnotation(['a' => 7]),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getPropertyAnnotations')
				->with($this, 'propertyName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations, $cachedReader->getPropertyAnnotations($this, 'propertyName'));
			$this->assertEquals($annotations, $cachedReader->getPropertyAnnotations($this, 'propertyName'));
		}

		public function testGetPropertyAnnotationsFiltered() {

			$annotations = [
				$this->getMockBuilder(Annotation::class)->getMock(),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getPropertyAnnotations')
				->with('className', 'propertyName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals([$annotations[1]], $cachedReader->getPropertyAnnotations('className', 'propertyName', [SampleAnnotation::class]));
			$this->assertEquals([$annotations[1]], $cachedReader->getPropertyAnnotations('className', 'propertyName', [SampleAnnotation::class]));
			$this->assertEquals($annotations, $cachedReader->getPropertyAnnotations('className', 'propertyName'));
		}

		public function testGetPropertyAnnotation() {

			$annotations = [
				$this->getMockBuilder(Annotation::class)->getMock(),
				new SampleAnnotation(['b' => 8]),
			];

			/** @var AnnotationReader|MockObject $readerMock */
			$readerMock = $this->getMockBuilder(AnnotationReader::class)->getMock();
			$readerMock
				->expects($this->once())
				->method('getPropertyAnnotations')
				->with('className', 'propertyName', [])
				->willReturn($annotations);

			$managerMock = $this->mockManager([]);

			$cachedReader = new CachedReader(new MemoryCache(), $managerMock, $readerMock);

			$this->assertEquals($annotations[1], $cachedReader->getPropertyAnnotation('className', 'propertyName', SampleAnnotation::class));
			$this->assertEquals($annotations[0], $cachedReader->getPropertyAnnotation('className', 'propertyName', get_class($annotations[0])));
		}

	}