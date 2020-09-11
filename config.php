<?php

define('DIR_ROOT', dirname(__FILE__));
define('ENVIRONMENT_FILE', DIR_ROOT . '/.environment');
define('DRIVER_DIR', DIR_ROOT . '/driver/');
define('TEMPLATE_DIR', DIR_ROOT . '/template/');

if(isset($_ENV['DATABASE_DRIVER']) && isset($_ENV['DATABASE_HOST']) && isset($_ENV['DATABASE_HOST_SECONDARY'])){
    $params = $_ENV;
}else{
    if (!file_exists(ENVIRONMENT_FILE)) die('File "' . ENVIRONMENT_FILE . '" not exist. Please create file.');
    $params = parse_ini_file(ENVIRONMENT_FILE, false, INI_SCANNER_RAW);
}

$requiredParams = array(
    'DATABASE_DRIVER',
    'DATABASE_ENCODING',
    'SAMPLE_DATA_LENGTH',

    'DATABASE_HOST',
    'DATABASE_PORT',
    'DATABASE_NAME',
    'DATABASE_USER',
    'DATABASE_PASSWORD',
    'DATABASE_DESCRIPTION',

    'DATABASE_HOST_SECONDARY',
    'DATABASE_PORT_SECONDARY',
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

define('FIRST_DSN',  DATABASE_DRIVER.'://'.DATABASE_USER.':'.DATABASE_PASSWORD.'@'.DATABASE_HOST.':'.DATABASE_PORT.'/'.DATABASE_NAME);
define('SECOND_DSN',  DATABASE_DRIVER.'://'.DATABASE_USER_SECONDARY.':'.DATABASE_PASSWORD_SECONDARY.'@'.DATABASE_HOST_SECONDARY.':'.DATABASE_PORT_SECONDARY.'/'.DATABASE_NAME_SECONDARY);