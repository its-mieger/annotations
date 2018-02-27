<?php
	/**
	 * Created by PhpStorm.
	 * User: chris
	 * Date: 27.02.18
	 * Time: 06:48
	 */

	namespace ItsMiegerAnnotationsTests\Cases\Cache;


	use ItsMieger\Annotations\Cache\MemoryCache;
	use ItsMiegerAnnotationsTests\Cases\TestCase;
	use ItsMiegerAnnotationsTests\Helper\SampleAnnotation;

	class MemoryCacheTest extends TestCase
	{
		public function testReadWrite() {
			$cache = new MemoryCache();

			$data = [
				'reader1' => [
					'class1' => [
						'method' => [
							'value' => [
								new SampleAnnotation(['a' => 7, 'b' => 8]),
								new SampleAnnotation(['a' => 9, 'b' => 10]),
								new SampleAnnotation(['a' => 9, 'b' => 10]),
							],
							'value2' => [
								new SampleAnnotation(['a' => 11, 'b' => 8]),
								new SampleAnnotation(['a' => 12, 'b' => 10]),
							]
						],
					    'class' => [
						    'cls'  => [
							    new SampleAnnotation(['a' => 7, 'b' => 8]),
							    new SampleAnnotation(['a' => 9, 'b' => 10]),
							    new SampleAnnotation(['a' => 9, 'b' => 10]),
						    ],
					    ]
					],
					'class2' => [
						'method' => [
							'value'  => [
								new SampleAnnotation(['a' => 7, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
							],
							'value2' => [
								new SampleAnnotation(['a' => 11, 'b' => 12]),
								new SampleAnnotation(['a' => 12, 'b' => 12]),
							]
						],
						'class'  => [
							'cls' => [
								new SampleAnnotation(['a' => 7, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
							],
						]
					]
				],
				'reader2' => [
					'class2' => [
						'method' => [
							'value'  => [
								new SampleAnnotation(['a' => 7, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 19]),
							],
							'value2' => [
								new SampleAnnotation(['a' => 11, 'b' => 9]),
								new SampleAnnotation(['a' => 12, 'b' => 9]),
							]
						],
						'class'  => [
							'cls' => [
								new SampleAnnotation(['a' => 7, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 9]),
							],
						]
					]
				]
			];


			// set
			foreach($data as $readerKey => $classes) {
				foreach($classes as $class => $memberTypes) {
					foreach($memberTypes as $memberType => $members) {
						foreach($members as $member => $annotations) {
							$this->assertSame($cache, $cache->put($readerKey, $class, $memberType, $member, $annotations));
						}
					}
				}
			}

			// read
			foreach ($data as $readerKey => $classes) {
				foreach ($classes as $class => $memberTypes) {
					foreach ($memberTypes as $memberType => $members) {
						foreach ($members as $member => $annotations) {
							$this->assertEquals($annotations, $cache->get($readerKey, $class, $memberType, $member));
						}
					}
				}
			}
		}


		public function testClear() {
			$cache = new MemoryCache();

			$data = [
				'reader1' => [
					'class1' => [
						'method' => [
							'value'  => [
								new SampleAnnotation(['a' => 7, 'b' => 8]),
								new SampleAnnotation(['a' => 9, 'b' => 10]),
								new SampleAnnotation(['a' => 9, 'b' => 10]),
							],
							'value2' => [
								new SampleAnnotation(['a' => 11, 'b' => 8]),
								new SampleAnnotation(['a' => 12, 'b' => 10]),
							]
						],
						'class'  => [
							'cls' => [
								new SampleAnnotation(['a' => 7, 'b' => 8]),
								new SampleAnnotation(['a' => 9, 'b' => 10]),
								new SampleAnnotation(['a' => 9, 'b' => 10]),
							],
						]
					],
					'class2' => [
						'method' => [
							'value'  => [
								new SampleAnnotation(['a' => 7, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
							],
							'value2' => [
								new SampleAnnotation(['a' => 11, 'b' => 12]),
								new SampleAnnotation(['a' => 12, 'b' => 12]),
							]
						],
						'class'  => [
							'cls' => [
								new SampleAnnotation(['a' => 7, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
								new SampleAnnotation(['a' => 9, 'b' => 12]),
							],
						]
					]
				],
				'reader2' => [
					'class2' => [
						'method' => [
							'value'  => [
								new SampleAnnotation(['a' => 7, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 19]),
							],
							'value2' => [
								new SampleAnnotation(['a' => 11, 'b' => 9]),
								new SampleAnnotation(['a' => 12, 'b' => 9]),
							]
						],
						'class'  => [
							'cls' => [
								new SampleAnnotation(['a' => 7, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 9]),
								new SampleAnnotation(['a' => 9, 'b' => 9]),
							],
						]
					]
				]
			];


			// set
			foreach ($data as $readerKey => $classes) {
				foreach ($classes as $class => $memberTypes) {
					foreach ($memberTypes as $memberType => $members) {
						foreach ($members as $member => $annotations) {
							$this->assertSame($cache, $cache->put($readerKey, $class, $memberType, $member, $annotations));
						}
					}
				}
			}

			// read
			foreach ($data as $readerKey => $classes) {
				foreach ($classes as $class => $memberTypes) {
					foreach ($memberTypes as $memberType => $members) {
						foreach ($members as $member => $annotations) {
							$this->assertEquals($annotations, $cache->get($readerKey, $class, $memberType, $member));
						}
					}
				}
			}

			// clear
			$cache->clear();

			// read
			foreach ($data as $readerKey => $classes) {
				foreach ($classes as $class => $memberTypes) {
					foreach ($memberTypes as $memberType => $members) {
						foreach ($members as $member => $annotations) {
							$this->assertSame(null, $cache->get($readerKey, $class, $memberType, $member));
						}
					}
				}
			}
		}
	}