<?php

namespace WeeklyBuddy;

use Flight;

/**
 * Init the application
 */
require_once './src/configs/init.php';
/**
 * Create routes mapping
 */
require_once './src/routing.php';

/**
 * Launch routing framework
 */
Flight::start();