# Img Tester

Test project for image upload with job that resize images.

Run with 

``` {.bash}
docker-compose up -d --build
```

## Useful Commands
To only restart one service, you can use the following command:

``` {.bash}
docker-compose up -d --no-deps --build <service_name>
```

If any changes has been done to the jobs, the queue worker will not pick up on
those changes. Running `php artisan queue:restart` is not possible since the
worker image currently is so small that it doesn't have bash. However, we can
use the command above instead.

## Setup

- Create jobs table and run migration

``` {.bash}
php artisan queue:table
php artisan queue:failed-table

php artisan migrate
```

- Make sure that we use the database driver for queues.

``` {.bash}
QUEUE_DRIVER=database
```

- As for now, processed images are stored in `storage/app/public`. A symlink
  should be created from `public/storage`, and can be made with 

``` {.bash}
php artisan storage:link
```
  
