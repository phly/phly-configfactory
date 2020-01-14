<?php
/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 * @copyright Copyright (c) Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-configfactory/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\ConfigFactory;

use Phly\ConfigFactory\ConfigFactory;
use Phly\ConfigFactory\ConfigKeyNotFoundException;
use Phly\ConfigFactory\InvalidServiceNameException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function file_exists;
use function file_put_contents;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;
use function var_export;

class ConfigFactoryTest extends TestCase
{
    protected $fileName;

    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory   = new ConfigFactory();
    }

    public function tearDown()
    {
        if ($this->fileName && file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testReturnsArrayForUnfoundKeysByDefault()
    {
        $this->assertAttributeSame(true, 'returnArrayForUnfoundKey', $this->factory);
    }

    public function testCanSetUnfoundKeyBehaviorViaConstructor()
    {
        $factory = new ConfigFactory(false);
        $this->assertAttributeSame(false, 'returnArrayForUnfoundKey', $factory);
    }

    public function unfoundKeyStates() : iterable
    {
        yield 'allowed' => [true];
        yield 'disallowed' => [false];
    }

    /**
     * @dataProvider unfoundKeyStates
     */
    public function testSerializationRetainsUnfoundKeyBehavior(bool $unfoundKeyState)
    {
        $this->fileName = tempnam(sys_get_temp_dir(), 'pecf');
        $factory        = new ConfigFactory($unfoundKeyState);

        file_put_contents($this->fileName, sprintf("<?php\nreturn %s;", var_export($factory, true)));
        $deserialized = include $this->fileName;

        $this->assertEquals($factory, $deserialized);
    }

    public function testRaisesExceptionForInvalidServiceName()
    {
        $this->container->get('config')->shouldNotBeCalled();
        $this->expectException(InvalidServiceNameException::class);
        ($this->factory)($this->container->reveal(), 'invalid-name');
    }

    public function configurationWithoutKey() : iterable
    {
        yield 'empty'            => [[]];
        yield 'first-level-only' => [['first' => []]];
        yield 'second-level'     => [['first' => ['second' => []]]];
        yield 'third-level'      => [['first' => ['second' => ['third' => []]]]];
    }

    /**
     * @dataProvider configurationWithoutKey
     */
    public function testRaisesExceptionIfKeyNotFoundInConfigAndFactoryConfiguredToRaiseErrorForUnfoundKey(array $config)
    {
        $this->container->get('config')->willReturn($config)->shouldBeCalledTimes(1);
        $factory = new ConfigFactory(false);

        $this->expectException(ConfigKeyNotFoundException::class);
        $factory($this->container->reveal(), 'config-first.second.third.fourth');
    }

    /**
     * @dataProvider configurationWithoutKey
     */
    public function testReturnsEmptyArrayIfKeyNotFoundInConfigAndFactoryConfiguredToRaiseErrorForUnfoundKey(
        array $config
    ) {
        $this->container->get('config')->willReturn($config)->shouldBeCalledTimes(1);
        $factory = new ConfigFactory(true);

        $config = $factory($this->container->reveal(), 'config-first.second.third.fourth');

        $this->assertEquals([], $config);
    }

    public function testUsesDotNotationToLocateNestedConfigurationArray()
    {
        $this->container
            ->get('config')
            ->willReturn([
                'first' => [
                    'second' => [
                        'third' => [
                            'fourth' => [
                                'some' => 'value',
                            ],
                        ],
                    ],
                ],
            ])
            ->shouldBeCalledTimes(1);
        $factory = new ConfigFactory(false);

        $config = $factory($this->container->reveal(), 'config-first.second.third.fourth');

        $this->assertEquals(['some' => 'value'], $config);
    }
}
