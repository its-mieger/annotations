# ItsMieger Annotations - Extended annotation library for laravel
This library allows easy implementation and usage of annotations for laravel projects. Unlike many
other annotation libraries it supports inheritance of annotations.

## Installation

If not using package auto-discovery, you must add the service provider to the providers array
in config/app.php

	ItsMieger\Annotations\Provider\AnnotationsServiceProvider::class,
	
Also remember to register the Events service provider. This allows the annotation cache being cleared
when other caches are cleared.

##Annotations
Since annotations are not a native language construct of PHP their implementation and syntax differs
slightly according to the used library. This library supports annotation syntax as shown by following
examples:

	@Annotation
	@Annotation arg1 arg2
	
	@Annotation(arg1, arg2)
	@Annotation(arg1, "argument 2")
	@Annotation(arg1=7, arg2=8)
	@Annotation(
		arg1,
		arg2
	)

First two annotations demonstrate the simple annotation syntax. Arguments are separated by whitespaces.

However sometimes the annotations require more complex parameters. Thus you may use the extended syntax
which is identified by an open brace **directly** after the annotation name. This allows you to use
quotes, specify argument keys or use multiple lines for your annotations.

Use `\` (backslash) to escape characters with special meaning, eg.: `"quote \" in text"`.
 
## Creating annotations
Implementing custom annotations is very easy. Simply extend the `AbstractAnnotation`-class:
	
	namespace Test\Name\Space

	class MyAnnotation extends AbstractAnnotation
	{
		/**
		 * @inheritDoc
		 */
		public function __construct(array $parameters) {
			parent::__construct($parameters);
			
			/* ... */
		}

	}
	
All parsed parameters are passed as (associative) array to the annotation's constructor. Remember to call
the parent constructor thus all other methods work as expected.

To use the above annotation you have to use the fully qualified class name in your doc comments:

	/**
	 * @Test\Name\Space\MyAnnotation
	 */

### Short annotation names
Since using fully qualified names can make code hard to read, you may register short names for your
annotations:

	Annotations::register(MyAnnotation::class, 'myAnnotation');
	
This allows you to use your annotation as follows:

	/**
	 * @myAnnotation
	 */

Of course the fully qualified name may still be used.


## Reading annotations
Annotations may be read using the `AnnotationReader`-facade:

	AnnotationReader::getClassAnnotations($cls, $filters = []);
	
This returns all annotations defined for a given class. You may pass a class name, an instance or
a reflection class. The second argument allows you to pass a list of annotations which should be
returned. Each element may be an annotation class name or the registered short name of an annotation. If
omitted, all found annotations are returned. The same annotation is returned multiple times if it is
found multiple times.

If you are only interested in a single annotation, the `getClassAnnotation`-method is what you need:

	AnnotationReader::getClassAnnotation($cls, $annotationName)
	
The second argument is the annotation class name or the registered short name. It returns the last
matching annotation found or `null` if not existing.
	
### Generic annotations

Annotations for which no class is found, are returned as `GenericAnnotation`. These annotations
return the annotation name via `getName` and all annotation parameters via their `getParameters`-method:

	/**
	 * @UnlinkedAnnotation(arg1, arg2)
	 */
	 
	$annotation->getName();
	// "UnlinkedAnnotation"
	
	$annotation->getParameters():
	// ["arg1", "arg2"]
	
To filter for generic annotations prefix the annotation name with `"generic:"`:

	AnnotationReader::getClassAnnotation($cls, "generic:UnlinkedAnnotation")
	


### Caching
Parsing annotations impacts the program performance. Because of this caching is used so that annotations
have not to parsed on every call. This implies that you have to clear the annotation cache if you modify
a part of your source code which affects your annotations. If you clear the application cache via
`artisan cache:clear`, the annotation cache is also cleared. If you want to clear annotations cache only,
use `artisan annotations:clear`.

By default, the PHP cache driver is used for annotations. This is a great choice for production
use it since it may benefit from the PHP  bytecode cache. However for development or testing, this cache
can be hard to use, since you would have to clear it over and over again.

Thus you may use the Memory cache driver for these environments, which only caches the annotations for the
lifetime of the request. You may change to cache used via the `ANNOTATION_CACHE` environment variable:

	ANNOTATION_CACHE=memory


### Ignored annotations
Many annotations exist for PHP which are only used for documentation or other purposes. These
annotations are ignored by default to improve performance and cache size. However the list can
be configured using the `ignore` option.

Another way is to explicitly register a class for an ignored annotation, thus it will not be
ignored anymore:
 
 	Annotations::register(MyVarAnnotation::class, 'var');


### Annotation inheritance
One of the main futures which differs from other annotation libraries is the support for inherited
annotations. This allows interfaces or parent class to define annotations which are inherited by
the implementing class:

	interface A {
		
		/**
		 * @InterfaceAnnotation
		 * @OverriddenAnnotation(fromInterface)
		 */
		 public function getValue();
	}
	
	class B implements A {
	
		/**
		 * @ClassAnnotation
		 * @OverriddenAnnotation(fromClass)
		 */
		 public function getValue();
	}
	
Use the `InheritedAnnotationReader` to also take inherited annotations into account when reading them:

	$annotations = InheritedAnnotationReader::getMethodAnnotations('B', 'getValue');
	
	// @InterfaceAnnotation
	// @ClassAnnotation	
	// @OverriddenAnnotation(fromInterface)
	
As you see interface annotations are added to the class annotations. In fact they also override the class
annotations of the same type.

The `InheritedAnnotationReader` returns only one annotation per type, since annotations of same type
override each other. This happens according to following rules:

* later annotations in a doc comment override previous annotations
* class annotations override parent-class annotations
* interface annotations override class annotations
* extend**ed**-interface annotations override extend**ing**-interface annotations
	
