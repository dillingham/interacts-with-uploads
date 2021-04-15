<?php

namespace Dillingham\InteractsWithUploads\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);


        include_once __DIR__.'/../database/migrations/create_posts_table.php.stub';

        (new \CreatePostsTable())->up();
    }
}
