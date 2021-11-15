<?php

/**
 * @see https://github.com/phly/phly-configfactory for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\ConfigFactory;

use ArrayObject;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

use function preg_match;

class ConfigAbstractFactory implements AbstractFactoryInterface
{
    use GetRequestedConfigTrait;

    /** @param string $requestedName */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return (bool) preg_match('/^config-/i', $requestedName);
    }

    /**
     * @param string $requestedName
     * @return array|ArrayObject
     * @throws InvalidServiceNameException If $serviceName does not begin with "config-".
     * @throws ConfigKeyNotFoundException If $returnArrayForUnfoundKey is false and the key is not found.
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return $this->getRequestedConfig($container, $requestedName);
    }
}
