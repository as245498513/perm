<?php

namespace Bloom\Tests;

use Bloom\Permission\PermissionServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Tests\TestCase;


class BaseTestCase extends TestCase
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app->register(PermissionServiceProvider::class);

        return $app;
    }
}
