<?php

/**
 * @see       https://github.com/phly/phly-configfactory for the canonical source repository
 */

declare(strict_types=1);

namespace PhlyTest\ConfigFactory;

use Phly\ConfigFactory\ConfigFactory;
use Phly\ConfigFactory\ConfigKeyNotFoundException;
use Phly\ConfigFactory\InvalidServiceNameException;
use PHPUnit\Framework\MockObject\MockObject;
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
    use DeprecatedAssertionsTrait;

    private MockObject&ContainerInterface $container;
    private ConfigFactory $factory;
    protected ?string $fileName = null;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory   = new ConfigFactory();
    }

    public function tearDown(): void
    {
        if ($this->fileName && file_exists($this->fileName)) {
            unlink($this->fileName);
            $this->fileName = null;
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

    public function unfoundKeyStates(): iterable
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
        $this->container
            ->expects($this->never())
            ->method('get')
            ->with('config');

        $this->expectException(InvalidServiceNameException::class);
        ($this->factory)($this->container, 'invalid-name');
    }

    public function configurationWithoutKey(): iterable
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
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new ConfigFactory(false);

        $this->expectException(ConfigKeyNotFoundException::class);
        $factory($this->container, 'config-first.second.third.fourth');
    }

    /**
     * @dataProvider configurationWithoutKey
     */
    public function testReturnsEmptyArrayIfKeyNotFoundInConfigAndFactoryConfiguredToRaiseErrorForUnfoundKey(
        array $config
    ) {
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new ConfigFactory(true);

        $config = $factory($this->container, 'config-first.second.third.fourth');

        $this->assertEquals([], $config);
    }

    public function testUsesDotNotationToLocateNestedConfigurationArray()
    {
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('config')
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
            ]);

        $factory = new ConfigFactory(false);

        $config = $factory($this->container, 'config-first.second.third.fourth');

        $this->assertEquals(['some' => 'value'], $config);
    }
}
