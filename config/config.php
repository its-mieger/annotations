<?php

	return [

		/*
		|--------------------------------------------------------------------------
		| Cache
		|--------------------------------------------------------------------------
		|
		| This option configures the cache to use for annotations. The cache
		| is used to store the annotations for each class and it's members.
		| Thus it is not needed to parse annotations or use reflection on each
		| lookup for annotations. For production use the "php" driver might be
		| the best choice since it uses the internal byte code cache of PHP. For
		| testing or development, the memory driver does not require clearing
		| cache after modifying annotations, classes or interfaces.
		|
		*/

		'cache' => env('ANNOTATION_CACHE', 'php'),

		/*
		|--------------------------------------------------------------------------
		| Cache engines
		|--------------------------------------------------------------------------
		|
		| This option configures the available caches. Which cache of these is used
		| is configured by the "cache" option.
		|
		*/
		'caches' => [
			'php'    => [
				'driver'    => 'php',
				'directory' => realpath(storage_path('annotations')),
			],
			'memory' => [
				'driver' => 'memory',
			]
		],

		/*
		|--------------------------------------------------------------------------
		| Ignored annotations
		|--------------------------------------------------------------------------
		|
		| This option configures the list annotations ignored by annotation readers.
		| Unless one of these annotations is explicitly registered, in the
		| annotation manager it will be ignored.
		|
		*/

		'ignore' => [
			// Annotation tags
			'Annotation', 'Attribute', 'Attributes',
			/* Can we enable this? 'Enum' , */
			'Required',
			'Target',
			// Widely used tags (but not existent in phpdoc)
			'fix', 'fixme',
			'override',
			// PHPDocumentor 1 tags
			'abstract', 'access',
			'code',
			'deprec',
			'endcode', 'exception',
			'final',
			'ingroup', 'inheritdoc', 'inheritDoc',
			'magic',
			'name',
			'toc', 'tutorial',
			'private',
			'static', 'staticvar', 'staticVar',
			'throw',
			// PHPDocumentor 2 tags.
			'api', 'author',
			'category', 'copyright',
			'deprecated',
			'example',
			'filesource',
			'global',
			'ignore',
			'internal',
			'license', 'link',
			'method',
			'package', 'param', 'property', 'property-read', 'property-write',
			'return',
			'see', 'since', 'source', 'subpackage',
			'throws', 'todo', 'TODO',
			'usedby', 'uses',
			'var', 'version',
			// PHPUnit tags
			'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd',
			// PHPCheckStyle
			'SuppressWarnings',
			// PHPStorm
			'noinspection',
			// PEAR
			'package_version',
			// PlantUML
			'startuml', 'enduml',
			// Symfony 3.3 Cache Adapter
			'experimental'
		],

	];
