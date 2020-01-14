<?php
/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 * @copyright Copyright (c) Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-configfactory/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\ConfigFactory;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

use function implode;
use function sprintf;

class ConfigKeyNotFoundException extends RuntimeException implements ContainerExceptionInterface
{
    public static function forNestedKey(array $keys) : self
    {
        return new self(sprintf(
            'Unable to find configuration for key %s',
            implode('.', $keys)
        ));
    }
}
