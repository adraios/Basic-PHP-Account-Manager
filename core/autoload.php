<?php
// Define base paths
define('BASE_PATH', __DIR__ . '/../');
define('CONTROLLER_PATH', BASE_PATH . 'controller/');    

/* Set error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . 'logs/error.log');*/

// Load common modules
require_once BASE_PATH . 'utils/utils.php';
require_once 'enums.php';
require_once 'logger.php';
require_once 'config.php';
require_once CONTROLLER_PATH . 'common/controller.php';
require_once 'errorHandler.php';
require_once 'db.php';
require_once 'languages.php';
require_once 'session.php';
require_once 'router.php';
