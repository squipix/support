<?php

declare(strict_types=1);

namespace Squipix\Support\Exceptions;

use Exception;

/**
 * Class     PackageException
 *
 * @author   SQUIPIX <info@squipix.com>
 */
class PackageException extends Exception
{
    public static function unspecifiedName(): self
    {
        return new static('You must specify the vendor/package name.');
    }
}
