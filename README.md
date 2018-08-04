# Img Tester

## Setup

- Create jobs table and run migration

``` {.bash}
php artisan queue:table

php artisan migrate
```

- Make sure that we use the database driver for queues.

``` {.bash}
QUEUE_DRIVER=database
```

