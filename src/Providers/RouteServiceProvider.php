<?php

declare(strict_types=1);

namespace Squipix\Support\Providers;

use Squipix\Support\Routing\Concerns\RegistersRouteClasses;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as IlluminateServiceProvider;

/**
 * Class     RouteServiceProvider
 *
 * @author   SQUIPIX <info@squipix.com>
 */
abstract class RouteServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use RegistersRouteClasses;
}
