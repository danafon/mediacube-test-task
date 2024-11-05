To start the application, you need to have composer installed.

Then, clone the repository, move to the `mediacube-test-task` directory and run the `launch.sh` file.

If that doesn't succeed, complete following steps manually:

 - run `composer install`
 ```
 composer install
 ```
 - check that `.env` file exists, if not - create it and fill it with contents of `.env.example`
 - start sail:
 ```
 ./vendor/bin/sail up
 ```
 - run the migrations:
 ```
 ./vendor/bin/sail artisan migrate:refresh
 ```
 - seed the databasae:
 ```
 ./vendor/bin/sail artisan db:seed
 ```

 Now the application should be available under [localhost/login](http://localhost/login). In addition, you should have an admin created, with email `admin@example.com` and password `1234`. Also the repository contains a file that lists all created API endpoints: `insomnia_endpoints`. To use it, import the file to your local insomnia app.