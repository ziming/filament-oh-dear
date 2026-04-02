<?php

namespace Ziming\FilamentOhDear\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ziming\FilamentOhDear\FilamentOhDear
 */
class FilamentOhDear extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ziming\FilamentOhDear\FilamentOhDear::class;
    }
}
