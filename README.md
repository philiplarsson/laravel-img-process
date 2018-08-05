# Img Tester

Test project for image upload with job that resize images.

Run with 

``` {.bash}
docker-compose up -d --build
```

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
  
