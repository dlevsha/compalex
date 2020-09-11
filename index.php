<?php
require_once 'config.php';

try {
    if (!defined('FIRST_DSN')) throw new Exception('Check your config.php file and uncomment settings section for your database');
    if (!strpos(FIRST_DSN, '://')) throw new Exception('Wrong dsn format');

    $pdsn = explode('://', FIRST_DSN);
    define('DRIVER', $pdsn[0]);

    if (!file_exists(DRIVER_DIR . DRIVER . '.php')) throw new Exception('Driver ' . DRIVER . ' not found');

    $firstDsnSplited = explode('/', FIRST_DSN);
    $secondDsnSplited = explode('/', SECOND_DSN);

    define('FIRST_BASE_NAME', end($firstDsnSplited));
    define('SECOND_BASE_NAME', end($secondDsnSplited));

    // abstract class
    require_once DRIVER_DIR . 'abstract.php';
    require_once DRIVER_DIR . DRIVER . '.php';

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'tables';

    $additionalTableInfo = array();
    switch ($action) {
        case "tables":
            $tables = Driver::getInstance()->getCompareTables();
            $additionalTableInfo = Driver::getInstance()->getAdditionalTableInfo();
            break;
        case "views":
            $tables = Driver::getInstance()->getCompareViews();
            break;
        case "procedures":
            $tables = Driver::getInstance()->getCompareProcedures();
            break;
        case "functions":
            $tables = Driver::getInstance()->getCompareFunctions();
            break;
        case "indexes":
            $tables = Driver::getInstance()->getCompareKeys();
            break;
        case "triggers":
            $tables = Driver::getInstance()->getCompareTriggers();
            break;
        case "rows":
            $rows = Driver::getInstance()->getTableRows($_REQUEST['baseName'], $_REQUEST['tableName']);
            break;
    }


    $basesName = array(
        'fArray' => FIRST_BASE_NAME,
        'sArray' => SECOND_BASE_NAME
    );

    if ($action == 'rows') {
        require_once TEMPLATE_DIR . 'rows.php';
    } else {
        require_once TEMPLATE_DIR . 'compare.php';
    }

} catch (Exception $e) {
    include_once TEMPLATE_DIR . 'error.php';
}

