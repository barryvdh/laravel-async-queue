# Laravel 4/5 Async Queue Driver

## Push a function/closure to the background.

Just like the 'sync' driver, this is not a real queue driver. It is always fired immediatly.
The only difference is that the closure is sent to the background without waiting for the response.
This package is more usable as an alternative for running incidental tasks in the background, without setting up a 'real' queue driver.

> **Note:** If you are coming from 0.1.0 (or dev-master), you will need to run the migrations, since the new versions uses a database to store the queue.

### Install

Require the latest version of this package with Composer

    composer require barryvdh/laravel-async-queue

Add the Service Provider to the providers array in config/app.php

    'Barryvdh\Queue\AsyncServiceProvider',

You need to run the migrations for this package

    $ php artisan migrate --package="barryvdh/laravel-async-queue"

Or publish them, so they are copied to your regular migrations

    $ php artisan migrate:publish barryvdh/laravel-async-queue

You should now be able to use the async driver in config/queue.php

    'default' => 'async',

    'connections' => array(
        ...
        'async' => array(
            'driver' => 'async',
        ),
        ...
    }

It should work the same as the sync driver, so no need to run a queue listener. Downside is that you cannot actually queue or plan things.
Queue::later() is also fired directly, but just runs `sleep($delay)` in background..
For more info see http://laravel.com/docs/queues

