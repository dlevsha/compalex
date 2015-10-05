<?php

if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    die('config.example.php rename the file to config.php');
}

try {
    if (!defined('FIRST_DSN')) {
        throw new Exception('Check your config.php file and uncomment settings section for your database');
    }
    if (!strpos(FIRST_DSN, '://')) {
        throw new Exception('Wrong dsn format');
    }

    $pdsn = explode('://', FIRST_DSN);
    define('DRIVER', $pdsn[0]);

    if (!file_exists(DRIVER_DIR . DRIVER . '.php')) {
        throw new Exception('Driver ' . DRIVER . ' not found');
    }

    define('FIRST_BASE_NAME', @end(explode('/', FIRST_DSN)));
    define('SECOND_BASE_NAME', @end(explode('/', SECOND_DSN)));

    // abstract class
    require_once DRIVER_DIR . 'abstract.php';
    require_once DRIVER_DIR . DRIVER . '.php';

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'tables';

    switch ($action) {
        case "tables":
            $tables = Driver::getInstance()->getCompareTables();
            break;
        case "views":
            $tables = Driver::getInstance()->getCompareViews();
            break;
        case "procedures":
            $tables = Driver::getInstance()->getCompareProcedures();
            break;
            $tables = Driver::getInstance()->getCompareFunctions();
        case "functions":
            break;
        case "keys":
            $tables = Driver::getInstance()->getCompareKeys();
            break;
        case "rows":
            $rows = Driver::getInstance()->getTableRows($_REQUEST['baseName'], $_REQUEST['tableName']);
            break;
    }

    if ($action == 'rows') {
        require_once TEMPLATE_DIR . 'rows.php';
    } else {
        require_once TEMPLATE_DIR . 'compare.php';
    }

} catch (Exception $e) {
    include_once TEMPLATE_DIR . 'error.php';
}