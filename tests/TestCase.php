<?php

namespace GrayLoon\FireChaser\Tests;

use GrayLoon\FireChaser\FireChaserServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.env', 'production');
        $app['config']->set('app.debug', false);
    }


    protected function getPackageProviders($app): array
    {
        return [
            FireChaserServiceProvider::class,
        ];
    }
}
