<?php
/**
 * @see       https://github.com/phly/phly-expressive-configfactory for the canonical source repository
 * @copyright Copyright (c) Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-expresive-configfactory/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\Expressive;

use Psr\Container\ContainerInterface;

use function array_key_exists;
use function array_merge;
use function array_shift;
use function explode;
use function preg_match;
use function substr;

class ConfigFactory
{
    /** @var bool */
    private $returnArrayForUnfoundKey;

    public static function __set_state(array $properties) : self
    {
        return new static($properties['returnArrayForUnfoundKey']);
    }

    public function __construct(bool $returnArrayForUnfoundKey = true)
    {
        $this->returnArrayForUnfoundKey = $returnArrayForUnfoundKey;
    }

    /**
     * @return array|ArrayObject
     * @throws InvalidServiceNameException if $serviceName does not begin with "config-"
     * @throws ConfigKeyNotFoundException if $returnArrayForUnfoundKey is false and the key is not found
     */
    public function __invoke(ContainerInterface $container, string $serviceName = 'config')
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
     * @throws ConfigKeyNotFoundException if $returnArrayForUnfoundKey is false and the key is not found
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
