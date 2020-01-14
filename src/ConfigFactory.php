<?php
/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 * @copyright Copyright (c) Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-configfactory/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\ConfigFactory;

use ArrayObject;
use Psr\Container\ContainerInterface;

class ConfigFactory
{
    use GetRequestedConfigTrait;

    /**
     * @return array|ArrayObject
     * @throws InvalidServiceNameException if $serviceName does not begin with "config-"
     * @throws ConfigKeyNotFoundException if $returnArrayForUnfoundKey is false and the key is not found
     */
    public function __invoke(ContainerInterface $container, string $serviceName = '')
    {
        return $this->getRequestedConfig($container, $serviceName);
    }
}
