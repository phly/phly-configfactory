<?php
/**
 * @see       https://github.com/phly/phly-expressive-configfactory for the canonical source repository
 * @copyright Copyright (c) Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-expresive-configfactory/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\Expressive;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

use function sprintf;

class InvalidServiceNameException extends RuntimeException implements ContainerExceptionInterface
{
    public static function forService(string $serviceName) : self
    {
        return new self(sprintf(
            'Only services beginning with "config-" may be constructed via the %s; received %s',
            ConfigFactory::class,
            $serviceName
        ));
    }
}
