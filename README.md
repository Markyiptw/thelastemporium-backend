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

8. Run database migration
    ```
    ./vendor/bin/sail artisan migrate
    ```
9. Create admin account
    ```
    ./vendor/bin/sail artisan create:admin {name} {email} {password}
    ```

# Example Frontend Integration

## Setup

```js
axios.defaults.withCredentials = true;
axios.defaults.baseURL = "http://localhost";
```

## Authentication

### Admin

```js
(async () => {
    await axios.get("/sanctum/csrf-cookie");
    await axios.post("/admin/login", {
        // those created with step 8 above
        email,
        password,
    });
    await axios.get("/api/admin"); // will return the authenticated admin if previous steps done correctly
})();
```

### User

Note: have to first clear cookie if previously logged in to admin

```js
(async () => {
    await axios.get("/sanctum/csrf-cookie");
    await axios.post("/login", {
        // can be created with admin account, see later sections
        email,
        password,
    });
    await axios.get("/api/user"); // will return the authenticated user if previous steps done correctly
    await axios.get("/api/object"); // will return the object belongs to the user if previous steps done correctly
})();
```

## Business Logics

### Create new user and object (admin only)

```js
(async () => {
    const response = await axios.post("/api/objects", {
        name: "obj0", // name of object
        user: {
            name: "usr0", // name of user,
            // for login
            email: "foo@example.com",
            password: "password",
        },
    });
})();
```

### Update Location

```js
(async () => {
    const response = await axios.post("/api/objects/1/locations", {
        latitude,
        longitude,
    });
})();
```

### List Locations for an Object

```js
(async () => {
    const response = await axios.get("/api/objects/1/locations");
})();
```

---
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
