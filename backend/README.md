# WeeklyBuddy BACKEND

[![Backend Status](https://github.com/PierreRainero/WeeklyBuddy/workflows/Backend_pipeline/badge.svg)](https://github.com/PierreRainero/WeeklyBuddy/actions?query=workflow%3ABackend_pipeline) [![codecov](https://codecov.io/gh/PierreRainero/WeeklyBuddy/branch/master/graph/badge.svg?token=XR93IT622L)](https://codecov.io/gh/PierreRainero/WeeklyBuddy)

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

## Launching linter

This project uses [phplint](https://github.com/overtrue/phplint) to ensure the syntax of the backend. In order to launch the linter use the following command :  
`./vendor/bin/phplint -c ./conf/phplint.yml`

## Launching tests

This project uses [PHPUnit](https://phpunit.de/index.html) to test the backend. In order to launch the tests suites use the following command :  
`./vendor/bin/phpunit --testdox -c ./conf/phpunit.xml`

## FAQ

### The requests routing isn't working

All the `.htaccess` files are made for my Apache server, if the [Flight](https://flightphp.com/) routing isn't working try the standard `.htacess` provided :

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### CORS header 'Access-Control-Allow-Origin' missing or not matching

If you try to install your own WeeklyBuddy instance or to launch it locally (in development mode) you will need to add your DNS to the authorized DNS list in the `Controller` mother class :

```php
private $dns_allowed= ['https://weeklybuddy.pierre-rainero.fr', 'https://www.weeklybuddy.pierre-rainero.fr', 'http://weeklybuddy.pierre-rainero.fr', 'http://www.weeklybuddy.pierre-rainero.fr' /* Add you DNS here */];
```

For development the frontend is launch at `http://localhost:3001` so you need to add it.
