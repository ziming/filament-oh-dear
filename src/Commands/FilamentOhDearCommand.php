<?php

namespace Ziming\FilamentOhDear\Commands;

use Illuminate\Console\Command;

class FilamentOhDearCommand extends Command
{
    public $signature = 'filament-oh-dear';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
