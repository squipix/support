<?php

declare(strict_types=1);

namespace Squipix\Support\Tests;

use Squipix\Support\Tests\Stubs\RouteServiceProviderWithRouteClasses;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @author   SQUIPIX <info@squipix.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            // For Testing
            RouteServiceProviderWithRouteClasses::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        //
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get fixture path
     *
     * @param  string  $path
     *
     * @return string
     */
    protected function getFixturesPath(string $path): string
    {
        return __DIR__.'/fixtures/'.$path;
    }
}
