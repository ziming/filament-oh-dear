<?php

namespace Ziming\FilamentOhDear\Exceptions;

use RuntimeException;

class MissingApiTokenException extends RuntimeException
{
    public static function make(): self
    {
        return new self('Missing Oh Dear API token. Set OH_DEAR_API_TOKEN or override the plugin configuration.');
    }
}
