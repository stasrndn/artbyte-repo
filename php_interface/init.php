<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/defines.php')) {
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/defines.php');
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/functions.php')) {
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/functions.php');
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/handlers.php')) {
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/handlers.php');
}