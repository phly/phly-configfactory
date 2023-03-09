<?php

/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 */

declare(strict_types=1);

namespace PhlyTest\ConfigFactory;

use Phly\ConfigFactory\ConfigAbstractFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ConfigAbstractFactoryTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private $container;
    private ConfigAbstractFactory $factory;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory   = new ConfigAbstractFactory();
    }

    public function testCanCreateReturnsFalseForInvalidServiceName()
    {
        $this->assertFalse($this->factory->canCreate($this->container, 'invalid-name'));
    }

    public function testCanCreateReturnsTrueForValidServiceName()
    {
        $this->assertTrue($this->factory->canCreate($this->container, 'config-some.key'));
    }
}
