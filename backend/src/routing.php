<?php

/**
 * This file list every route/controller mapping
 */

defineRoute('POST', '/users', function() use ($dice) {
    $usersController = $dice->create('WeeklyBuddy\Controllers\UsersController');
    $usersController->addUser(Flight::request())->send();
}, $dice);

defineRoute('POST', '/users/connection', function() use ($dice) {
    $usersController = $dice->create('WeeklyBuddy\Controllers\UsersController');
    $usersController->authentifyUser(Flight::request())->send();
}, $dice);

defineRoute('GET', '/users/activate', function() use ($dice) {
    $usersController = $dice->create('WeeklyBuddy\Controllers\UsersController');
    $usersController->activeUser(Flight::request())->send();
}, $dice);

/**
 * Add a route to the exposed routes
 * @param string $method HTTP method used
 * @param string $path Relative path for the exposed route
 * @param function $action Action to do when the route is invoked
 * @param object $dice Dice instance to create controllers and services
 * @return void
 */
function defineRoute($method, $path, $action, $dice) {
    Flight::route("OPTIONS $path", function() use ($dice) {
        $controller = $dice->create('WeeklyBuddy\Controllers\Controller');
        $controller->preflightRequest()->send();
    });
    Flight::route("$method $path", $action);
}