# phly-expressive-configfactory

[![Build Status](https://secure.travis-ci.org/phly/phly-expressive-configfactory.svg?branch=master)](https://secure.travis-ci.org/phly/phly-expressive-configfactory)
[![Coverage Status](https://coveralls.io/repos/github/phly/phly-expressive-configfactory/badge.svg?branch=master)](https://coveralls.io/github/phly/phly-expressive-configfactory?branch=master)

This library provides a re-usable factory for pulling configuration from nested
keys.

## Installation

Run the following to install this library:

```bash
$ composer require phly/phly-expressive-configfactory
```

## Usage

Assign the factory `Phly\Expressive\ConfigFactory` to services named with the
following structure:

```text
config-<dot.separated.config.keys>
```

As an example, if you have the following structure:

```php
return [
    'cache' => [
        'adapters' => [
            'blog' => [
                'connection' => 'tcp://localhost:6349',
                'username'   => 'www-data',
                'prefix'     => 'blog',
            ],
        ],
    ],
];
```

and you wanted the "blog" adapter configuration, you would assign the dependency
as follows:

```php
return [
    'dependencies' => [
        'factories' => [
            'config-cache.adapters.blog' => \Phly\Expressive\ConfigFactory,
        ],
    ],
];
```

### Return empty or raise exception

By default, if no configuration at the expected key is found, the factory
returns an empty array. If you want it to instead raise an exception, you can
assign the factory as follows:

```php
return [
    'dependencies' => [
        'factories' => [
            'config-cache.adapters.blog' => new \Phly\Expressive\ConfigFactory(false),
        ],
    ],
];
```

> This operation is safe, as `ConfigFactory` implements `__set_state()`,
> allowing it to be serialized safely with `var_export()`.

The exception will indicate the key hierarchy it was attempting to retrieve.

### Using configuration in factories

In your factories, you will refer to the metaname when retrieving the service.
Following our example above:

```php
use Psr\Container\ContainerInterface;

class BlogCacheFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Cache($container->get('config-cache.adapters.blog'));
    }
}
```

### Abstract Factory

> Since 1.1.0

If you are using [zend-servicemanager](https://docs.zendframework.com/zend-servicemanager),
you can use the class `Phly\Expressive\ConfigAbstractFactory` as an abstract
factory. This allows you to omit adding a factory entry for every configuration
segment you want to retrieve. Instead, you can add the following:

```php
return [
    'dependencies' => [
        'abstract_factories' => [
            \Phly\Expressive\ConfigAbstractFactory::class,

            // OR

            new \Phly\Expressive\ConfigAbstractFactory(false),
        ],
    ],
];
```

When present, it will handle any services with the prefix `config-`, and operate
in the same way as the `ConfigFactory`.

### Caveats

You should only specify keys that will return an array. Most containers only
allow returning an array or object from factories, and will raise an exception
otherwise. For those requiring an object, Expressive generally casts to an
`ArrayObject` instance, making this safe.

## Support

* [Issues](https://github.com/zendframework/phly-expressive-configfactory/issues/)
