<?php
$host = $_SERVER['HTTP_HOST'];
$serverName = $_SERVER['SERVER_NAME'];

if ($host === 'localhost' ||
    $host === '127.0.0.1' ||
    $serverName === 'localhost' ||
    $serverName === '127.0.0.1' ||
    strpos($host, 'localhost:') === 0 ||
    strpos($host, '127.0.0.1:') === 0){

        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', 'ventacoches');
        define('URL_ADMIN', 'http://localhost/coches');

    }else{
        define('DB_HOST', 'db5018152600.hosting-data.io');
        define('DB_USER', 'dbu1545378');
        define('DB_PASS', '123Fran*');
        define('DB_NAME', 'dbs14399139');
        define('URL_ADMIN', 'http://www.alumnfran.com/admin');

    }