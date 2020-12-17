<?php

namespace WeeklyBuddy;

use Flight;

/**
 * Init the application
 */
require_once(join(DIRECTORY_SEPARATOR, array(__DIR__, 'src', 'configs', 'init.php')));

/**
 * Create routes mapping
 */
require_once(join(DIRECTORY_SEPARATOR, array(__DIR__, 'src', 'routing.php')));

/**
 * Launch routing framework
 */
Flight::start();