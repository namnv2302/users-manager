<?php

define('_MODULE_DEFAULT', 'home');
define('_ACTION_DEFAULT', 'list');

const _INCODE = true;

// Thiết lập host
define('_WEB_HOST_ROOT', 'http://'.$_SERVER['HTTP_HOST'].'/users-manager');
define('_WEB_HOST_TEMPLATE', _WEB_HOST_ROOT.'/templates');

// Thiết lập path
define('_WEB_PATH_ROOT', __DIR__);
define('_WEB_PATH_TEMPLATE', _WEB_PATH_ROOT.'/templates');

// Thiết lập kết nối database
const _HOST = 'localhost';
const _USER = 'root';
const _PASS = '';
const _DB = 'phponline';
const _DRIVER = 'mysql';
