<?php

declare(strict_types=1);

namespace PhlyTest\ConfigFactory;

use PHPUnit\Framework\Assert;
use ReflectionProperty;

use function sprintf;
use function var_export;

trait DeprecatedAssertionsTrait
{
    /**
     * @param mixed $expected
     */
    public function assertAttributeSame($expected, string $property, object $instance, string $message = ''): void
    {
        $r = new ReflectionProperty($instance, $property);
        $r->setAccessible(true);
        $actual = $r->getValue($instance);

        $message = $message !== ''
            ? $message
            : sprintf(
                'Unable to assert that property %s of instance %s with value "%s" is identical to "%s"',
                $property,
                $instance::class,
                var_export($actual, true),
                var_export($expected, true)
            );

        Assert::assertSame($expected, $actual, $message);
    }
}
