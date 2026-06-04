<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Support\Facades\Artisan;

Artisan::command('about:chefvirtuo', function (): void {
    /** @var ClosureCommand $this */
    $this->info('ChefVirtuo lecturer dashboard is ready.');
})->purpose('Display ChefVirtuo dashboard status');
