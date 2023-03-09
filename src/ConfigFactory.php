<?php

/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\ConfigFactory;

use ArrayAccess;
use Psr\Container\ContainerInterface;

class ConfigFactory
{
    use GetRequestedConfigTrait;

    /**
     * @throws InvalidServiceNameException If $serviceName does not begin with "config-".
     * @throws ConfigKeyNotFoundException If $returnArrayForUnfoundKey is false and the key is not found.
     */
    public function __invoke(ContainerInterface $container, string $serviceName = ''): array|ArrayAccess
    {
        return $this->getRequestedConfig($container, $serviceName);
    }
}
