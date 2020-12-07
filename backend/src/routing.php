<?php

/**
 * This file list every route/controller mapping
 */

Flight::route('POST /users', function() use ($dice) {
    $usersController = $dice->create('WeeklyBuddy\Controllers\UsersController');
    $usersController->addUser(Flight::request());
});

Flight::route('POST /users/connection', function() use ($dice) {
    $usersController = $dice->create('WeeklyBuddy\Controllers\UsersController');
    $usersController->authentifyUser(Flight::request());
});

Flight::route('GET /users/activate', function() use ($dice) {
    $usersController = $dice->create('WeeklyBuddy\Controllers\UsersController');
    $usersController->activeUser(Flight::request());
});