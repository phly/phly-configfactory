<?php

/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\ConfigFactory;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

use function sprintf;

class InvalidServiceNameException extends RuntimeException implements ContainerExceptionInterface
{
    public static function forService(string $serviceName): self
    {
        return new self(sprintf(
            'Only services beginning with "config-" may be constructed via the %s; received %s',
            ConfigFactory::class,
            $serviceName
        ));
    }
}
