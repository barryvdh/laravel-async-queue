# Laravel Async Queue Driver
## Push a function/closure to the background.

Just like the 'sync' driver, this is not a real queue driver. It is always fired immediatly.
The only difference is that the closure is sent to the background without waiting for the response.

> **Note:** If you are coming from 0.1.0 (or dev-master), you will need to run the migrations, since the new versions uses a database to store the queue.

### Install
Add the package to the require section of your composer.json and run `composer update`

    "barryvdh/laravel-async-queue": "dev-master"

Add the Service Provider to the providers array in config/app.php

    'Barryvdh\Queue\AsyncServiceProvider',
    
You need to run the migrations for this package

    $ php artisan migrate --package="barryvdh/laravel-async-queue"

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
Queue::later() is also fired directly.
For more info see http://laravel.com/docs/queues

