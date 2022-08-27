# Running Local Dev Server

1. Clone the Repo

2. Navigate to the application's directory

    ```
    cd thelastemporium-backend
    ```

3. Copy the .env.example as .env

    ```
    cp .env.example .env
    ```

4. Run the following command from [here](https://laravel.com/docs/9.x/sail#installing-composer-dependencies-for-existing-projects):

    ```
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/var/www/html \
        -w /var/www/html \
        laravelsail/php81-composer:latest \
        composer install --ignore-platform-reqs
    ```

5. Start the server

    ```
    ./vendor/bin/sail up // recommended for first time running so you can see when services are ready
    ./vendor/bin/sail up -d // later you can run it as daemon
    ```

6. Generate the APP_KEY environment variable

    ```
    ./vendor/bin/sail php artisan key:generate
    ```

7. (Optional) set alias for sail
   See https://laravel.com/docs/9.x/sail#configuring-a-shell-alias
