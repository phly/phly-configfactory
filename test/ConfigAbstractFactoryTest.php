<?php
/**
 * @see       https://github.com/phly/phly-expressive-configfactory for the canonical source repository
 * @copyright Copyright (c) Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-expresive-configfactory/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\Expressive;

use Interop\Container\ContainerInterface;
use Phly\Expressive\ConfigAbstractFactory;
use PHPUnit\Framework\TestCase;

class ConfigAbstractFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class)->reveal();
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
