<?php

declare(strict_types=1);

namespace Squipix\Support\Tests\Providers;

use Squipix\Support\Exceptions\PackageException;
use Squipix\Support\Tests\Stubs\{InvalidPackageServiceProvider, TestPackageServiceProvider};
use Squipix\Support\Tests\TestCase;

/**
 * Class     PackageServiceProviderTest
 *
 * @author   SQUIPIX <info@squipix.com>
 */
class PackageServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Squipix\Support\Tests\Stubs\TestPackageServiceProvider */
    private $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new TestPackageServiceProvider($this->app);

        $this->provider->register();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Squipix\Support\Providers\ServiceProvider::class,
            \Squipix\Support\Providers\PackageServiceProvider::class,
            \Squipix\Support\Tests\Stubs\TestPackageServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_register_config(): void
    {
        $config = config('package');

        static::assertArrayHasKey('foo', $config);
        static::assertEquals('bar', $config['foo']);
    }

    /** @test */
    public function it_must_throw_a_package_exception(): void
    {
        $this->expectException(PackageException::class);
        $this->expectExceptionMessage('You must specify the vendor/package name.');

        (new InvalidPackageServiceProvider($this->app))->register();
    }
}
