<?php

declare(strict_types=1);

namespace Squipix\Support\Tests\Database;

use RuntimeException;
use Squipix\Support\Database\Seeder as SupportSeeder;
use Squipix\Support\Tests\TestCase;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder as IlluminateSeeder;

/**
 * Class     SeederTest
 *
 * @author   SQUIPIX <info@squipix.com>
 */
class SeederTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function tearDown(): void
    {
        Eloquent::reguard();

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_reguards_even_if_a_seed_fails(): void
    {
        Eloquent::reguard();

        $failingSeeder = new class extends IlluminateSeeder
        {
            public function run(): void
            {
                throw new RuntimeException('Boom');
            }
        };

        $seeder = new class($failingSeeder::class) extends SupportSeeder
        {
            public function __construct(string $seedClass)
            {
                $this->seeds = [$seedClass];
            }
        };

        try {
            $seeder->run();
        } catch (RuntimeException $exception) {
            static::assertSame('Boom', $exception->getMessage());
        }

        static::assertFalse(Eloquent::isUnguarded());
    }
}
