<?php
	namespace ItsMiegerAnnotationsTests\Cases\Parser;


	use ItsMieger\Annotations\Parser\AnnotationParser;
	use ItsMiegerAnnotationsTests\Cases\TestCase;

	class AnnotationParserTest extends TestCase
	{
		public function testParseDocComment() {

			$comment = '/**
			* This is some text
			* \@annoX
			* {@annoLink}
			* @anno
			* @anno2
			* @anno3()						
			* @anno4(asd, bgsd)						
			* @anno5(asd, b=bgsd)	
			* @anno
			* @anno6(@asd, b=bgsd)	
			* @anno7(asd, "b=bgsd")	
			* @anno8(asd, "b=bg\\"sd")
			* @anno9@anno10
			* @anno11(asd, bgsd) bsad						
			* @anno12(
			*   asd, 
			*   bgsd
			* )
			* @anno13(asd, b = bgsd)
			* @My\Annotation\Full\Name
			* @anno14 ,"\'n {
			* @anno15 a=asd, b=bgsd
			*/';

			$annotations = (new AnnotationParser())->parseDocComment($comment);

			// anno
			$currAnnotation = $annotations[0];
			$this->assertEquals('anno', $currAnnotation->getName());
			$this->assertEquals([], $currAnnotation->getParameters());

			// anno2
			$currAnnotation = $annotations[1];
			$this->assertEquals('anno2', $currAnnotation->getName());
			$this->assertEquals([], $currAnnotation->getParameters());

			// anno3
			$currAnnotation = $annotations[2];
			$this->assertEquals('anno3', $currAnnotation->getName());
			$this->assertEquals([], $currAnnotation->getParameters());

			// anno4
			$currAnnotation = $annotations[3];
			$this->assertEquals('anno4', $currAnnotation->getName());
			$this->assertEquals(['asd', 'bgsd'], $currAnnotation->getParameters());

			// anno5
			$currAnnotation = $annotations[4];
			$this->assertEquals('anno5', $currAnnotation->getName());
			$this->assertEquals(['asd', 'b' => 'bgsd'], $currAnnotation->getParameters());

			// anno
			$currAnnotation = $annotations[5];
			$this->assertEquals('anno', $currAnnotation->getName());
			$this->assertEquals([], $currAnnotation->getParameters());

			// anno6
			$currAnnotation = $annotations[6];
			$this->assertEquals('anno6', $currAnnotation->getName());
			$this->assertEquals(['@asd', 'b' => 'bgsd'], $currAnnotation->getParameters());

			// anno7
			$currAnnotation = $annotations[7];
			$this->assertEquals('anno7', $currAnnotation->getName());
			$this->assertEquals(['asd', 'b=bgsd'], $currAnnotation->getParameters());

			// anno8
			$currAnnotation = $annotations[8];
			$this->assertEquals('anno8', $currAnnotation->getName());
			$this->assertEquals(['asd', 'b=bg"sd'], $currAnnotation->getParameters());

			// anno9
			$currAnnotation = $annotations[9];
			$this->assertEquals('anno9', $currAnnotation->getName());
			$this->assertEquals([], $currAnnotation->getParameters());

			// anno10
			$currAnnotation = $annotations[10];
			$this->assertEquals('anno10', $currAnnotation->getName());
			$this->assertEquals([], $currAnnotation->getParameters());

			// anno11
			$currAnnotation = $annotations[11];
			$this->assertEquals('anno11', $currAnnotation->getName());
			$this->assertEquals(['asd', 'bgsd'], $currAnnotation->getParameters());

			// anno12
			$currAnnotation = $annotations[12];
			$this->assertEquals('anno12', $currAnnotation->getName());
			$this->assertEquals(['asd', 'bgsd'], $currAnnotation->getParameters());

			// anno13
			$currAnnotation = $annotations[13];
			$this->assertEquals('anno13', $currAnnotation->getName());
			$this->assertEquals(['asd', 'b' => 'bgsd'], $currAnnotation->getParameters());

			// My\Annotation\Full\Name
			$currAnnotation = $annotations[14];
			$this->assertEquals('My\Annotation\Full\Name', $currAnnotation->getName());
			$this->assertEquals([], $currAnnotation->getParameters());

			// anno14
			$currAnnotation = $annotations[15];
			$this->assertEquals('anno14', $currAnnotation->getName());
			$this->assertEquals([',"\'n', '{'], $currAnnotation->getParameters());

			// anno15
			$currAnnotation = $annotations[16];
			$this->assertEquals('anno15', $currAnnotation->getName());
			$this->assertEquals(['a=asd,', 'b=bgsd'], $currAnnotation->getParameters());
		}


	}