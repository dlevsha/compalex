<?php

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

define('FIRST_DATABASE_NAME', 'Production database');
define('SECOND_DATABASE_NAME', 'Developer database');

// \\ --- Please edit this section --- //

define('DATABASE_ENCODING', 'utf8');

define('SAMPLE_DATA_LENGTH', 100);


define('DIR_ROOT', dirname(__FILE__));

define('DRIVER_DIR', DIR_ROOT . '/driver/');
define('TEMPLATE_DIR', DIR_ROOT . '/template/');