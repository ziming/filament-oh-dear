<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('package code does not depend on workbench')
    ->expect('Ziming\\FilamentOhDear')
    ->not->toUse('Workbench\\App');
