<?php

declare(strict_types=1);

namespace Squipix\Support\Database;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder as IlluminateSeeder;

/**
 * Class     Seeder
 *
 * @author   SQUIPIX <info@squipix.com>
 */
abstract class Seeder extends IlluminateSeeder
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Seeder collection.
     *
     * @var array
     */
    protected $seeds = [];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Eloquent::unguard();

        try {
            foreach ($this->seeds as $seed) {
                $this->call($seed);
            }
        } finally {
            Eloquent::reguard();
        }
    }
}
