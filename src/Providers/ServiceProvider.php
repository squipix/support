<?php

declare(strict_types=1);

namespace Squipix\Support\Providers;

use Squipix\Support\Providers\Concerns\InteractsWithApplication;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class     ServiceProvider
 *
 * @author   SQUIPIX <info@squipix.com>
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use InteractsWithApplication;
}
