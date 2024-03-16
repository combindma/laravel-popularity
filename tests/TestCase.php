<?php

namespace Combindma\Popularity\Tests;

use Combindma\Popularity\PopularityServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            PopularityServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('app.locale', 'fr');

        $page = include __DIR__.'/migrations/create_page_table.php';
        $migration = include __DIR__.'/../database/migrations/create_visits_table.php.stub';
        $page->up();
        $migration->up();
    }
}
