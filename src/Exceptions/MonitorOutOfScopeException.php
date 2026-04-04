<?php

namespace Ziming\FilamentOhDear\Exceptions;

use RuntimeException;

class MonitorOutOfScopeException extends RuntimeException
{
    public static function forMonitor(int $monitorId): self
    {
        return new self("Monitor [{$monitorId}] is not available in the configured scope.");
    }
}
