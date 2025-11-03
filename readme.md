# Laravel Async Queue Driver

## Push a function/closure to the background.

Just like the 'sync' or 'deferred' connection, this is not a real queue. It is always fired immediately.
The only difference is that the closure is sent to the background without waiting for the response.
This package is more usable as an alternative for running incidental tasks in the background, without setting up a 'real' queue driver.
It is similar to the 'deferred' connection, but it runs in a background process, so might be more suitable for long running tasks.

> Note: Since v0.8 this uses the Concurrently::defer() method instead of the database queue. No database migrations tables are required now. The config can be simplified as below.
> 
### Install

Require the latest version of this package with Composer

    composer require barryvdh/laravel-async-queue

You should now be able to use the async driver in config/queue.php. Use the same config as for the database, but use async as driver.

    'connections' => array(
        ...
        'async' => array(
            'driver' => 'async',
        ),
        ...
    }

Set the default to `async`, either by changing to config or setting `QUEUE_DRIVER` in your `.env` file to `async`.

It should work the same as the sync driver, so no need to run a queue listener. Downside is that you cannot actually queue or plan things. Queue::later() is also fired directly. For more info see http://laravel.com/docs/queues

