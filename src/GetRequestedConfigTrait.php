<?php

/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\ConfigFactory;

use ArrayObject;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function array_merge;
use function array_shift;
use function explode;
use function preg_match;
use function substr;

trait GetRequestedConfigTrait
{
    /** @var bool */
    private $returnArrayForUnfoundKey;

    public static function __set_state(array $properties): self
    {
        return new static($properties['returnArrayForUnfoundKey']);
    }

    public function __construct(bool $returnArrayForUnfoundKey = true)
    {
        $this->returnArrayForUnfoundKey = $returnArrayForUnfoundKey;
    }

    /**
     * @return array|ArrayObject
     * @throws InvalidServiceNameException If $serviceName does not begin with "config-".
     * @throws ConfigKeyNotFoundException If $returnArrayForUnfoundKey is false and the key is not found.
     */
    private function getRequestedConfig(ContainerInterface $container, string $serviceName)
    {
        if (! preg_match('/^config-/i', $serviceName)) {
            throw InvalidServiceNameException::forService($serviceName);
        }

        $config = $container->get('config');
        $key    = substr($serviceName, 7);
        $keys   = explode('.', $key);

        return $this->getConfigForKeys($config, $keys);
    }

    /**
     * @return array|ArrayObject
     * @throws ConfigKeyNotFoundException If $returnArrayForUnfoundKey is false and the key is not found.
     */
    private function getConfigForKeys(array $config, array $keys, array $parentKeys = [])
    {
        if (empty($keys)) {
            return $config;
        }

        $key = array_shift($keys);

        if (! array_key_exists($key, $config)) {
            if ($this->returnArrayForUnfoundKey) {
                return [];
            }

            throw ConfigKeyNotFoundException::forNestedKey(array_merge([$key], $parentKeys));
        }

        $parentKeys[] = $key;
        return $this->getConfigForKeys($config[$key], $keys, $parentKeys);
    }
}
