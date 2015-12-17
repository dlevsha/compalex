<?php

define('DIR_ROOT', dirname(__FILE__));
define('ENVIRONMENT_FILE', DIR_ROOT . '/.environment');
define('DRIVER_DIR', DIR_ROOT . '/driver/');
define('TEMPLATE_DIR', DIR_ROOT . '/template/');

if (!file_exists(ENVIRONMENT_FILE)) die('File "' . ENVIRONMENT_FILE . '" not exist. Please create file.');
$params = parse_ini_file(ENVIRONMENT_FILE);

$requiredParams = array(
    'DATABASE_DRIVER',
    'DATABASE_ENCODING',
    'SAMPLE_DATA_LENGTH',

    'DATABASE_HOST',
    'DATABASE_NAME',
    'DATABASE_USER',
    'DATABASE_PASSWORD',
    'DATABASE_DESCRIPTION',

    'DATABASE_HOST_SECONDARY',
    'DATABASE_NAME_SECONDARY',
    'DATABASE_USER_SECONDARY',
    'DATABASE_PASSWORD_SECONDARY',
    'DATABASE_DESCRIPTION_SECONDARY',
);

array_map(function ($name) use ($params) {
    if (!isset($params[$name])) {
        die('Param ' . $name . ' not set in file ' . ENVIRONMENT_FILE);
    }else{
        define($name, $params[$name]);
    }
}, $requiredParams);

define('FIRST_DSN',  DATABASE_DRIVER.'://'.DATABASE_USER.':'.DATABASE_PASSWORD.'@'.DATABASE_HOST.'/'.DATABASE_NAME);
define('SECOND_DSN',  DATABASE_DRIVER.'://'.DATABASE_USER_SECONDARY.':'.DATABASE_PASSWORD_SECONDARY.'@'.DATABASE_HOST_SECONDARY.'/'.DATABASE_NAME_SECONDARY);


// --- Please edit this section --- //

// MSSQL sample config
// define('FIRST_DSN',  'dblib://login:password@ExternalHost/database1');
// define('SECOND_DSN', 'dblib://login:password@ExternalHost/database2');

// MySQL sample config
// define('FIRST_DSN',  'mysql://login:password@localhost/compalex_test_1');
// define('SECOND_DSN', 'mysql://login:password@localhost/compalex_test_2');

// PGSQL sample config
// define('FIRST_DSN',  'pgsql://login:password@localhost:5432/database1');
// define('SECOND_DSN', 'pgsql://login:password@localhost:5432/database2');