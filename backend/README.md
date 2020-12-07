# WeeklyBuddy BACKEND

## Getting started

To use this project [Composer](https://getcomposer.org/) and a recent PHP version are required. As Maven for Java projects, NPM for Node projects,... it's a packages manager providing an easy way to include external libraries.  
To start you need to follow several steps :

1. Installing dependencies :  
**For development :**  
`composer install`  
**For production :**  
`composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader`  
2. Update dependencies :  
`composer update`  
3. Create autoloader file (psr-4) to autoload dependencies and use php namespaces as "packages" :  
`composer dump-autoload`  
4. Create the environment files. To make this application working it's necessary to define variables. You will need to have following files :  

    ```noformat
    weeklybuddy
    ├── backend
    ├── env
    │    ├── db.php
    │    ├── jwt.php
    │    └── smtp.php
    └── frontend
    ```

    **db.php** :

    ```php
    return array(
        'driver'   => 'my_driver',
        'user'     => 'username',
        'password' => 'user password',
        'host'     => 'host:port',
        'dbname'   => 'databasename'
    );
    ```

    **jwt.php** :

    ```php
    return 'mySecret';
    ```

    **smtp.php** :

    ```php
    return array(
        'auth'          => true, // true or false
        'username'      => 'username',
        'password'      => 'user password',
        'host'          => 'smtp host',
        'port'          => 0, // smtp port
        'protocol'      => 'tls', // smtp protocol : ssl or tls
        'emitter'       => 'emitter email',
        'api-domain'    => 'domain exposing the backend'
    );
    ```

    :information_source: It's your responsibility to protect the folder "env" (and it's pretty important because there are sensitive informations inside).

5. Setup you database with all scripts inside the "sql" folder (pass them in alphabetical order).

## FAQ

### The requests routing isn't working

All the `.htaccess` files are made for my Apache server, if the [Flight](https://flightphp.com/) routing isn't working try the standard `.htacess` provided :  

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```