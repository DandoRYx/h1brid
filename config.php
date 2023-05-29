<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

// -- Core --
define('ROOT', str_replace('\\', '/', __DIR__) . '/');

// -- Internal --
define('PAGE', 'https://example.com/');
define('EXTENSION', '.php');
define('COOKIE_PREFIX', 'h1brid_');
define('COOKIE_INFINITE', 'Wed, 01 Jan 3000 00:00:00 GMT');
define('HTTPS', true);
define('MAIN_VIEW', 'home');
define('TRAILING_SLASH', false);

// -- Page --
define('OWNER', 'H1BRID');
define('THEME_COLOR', '#FFFFFF');
define('TITLE_PREFIX', '');

// -- Language --
define('MULTI_LANGUAGE', false);
define('DEFAULT_LANGUAGE', 'en');

// -- Paths --
define('_APP', ROOT . 'app/');
define('_PUBLIC', ROOT . 'public/');

define('LOG', _APP . '.log');
define('LOCALHOST', file_exists(_APP . '.localhost'));

define('CORE', _APP . 'core/');
define('DATA', _APP . 'data/');
define('LANGUAGES', _APP . 'languages/');

// -- MVC --
define('VIEWS', _APP . 'mvc/views/');
define('MODELS', _APP . 'mvc/models/');
define('CONTROLLERS', _APP . 'mvc/controllers/');

// -- Database --
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_HOST_LOCALHOST', 'localhost');
define('DB_NAME_LOCALHOST', 'h1brid');
define('DB_USER_LOCALHOST', 'root');
define('DB_PASS_LOCALHOST', '');

// -- Required classes --
require CORE . 'Router' . EXTENSION;
require CORE . 'Language' . EXTENSION;
require CORE . 'Page' . EXTENSION;
require CORE . 'Api' . EXTENSION;
require CORE . 'Database' . EXTENSION;
require CORE . 'Log' . EXTENSION;
require CORE . 'Cookie' . EXTENSION;
require CORE . 'Sitemap' . EXTENSION;
require CORE . 'Breadcrumb' . EXTENSION;
require CORE . 'Email' . EXTENSION;
require CORE . 'Font' . EXTENSION;
require CORE . 'Utils' . EXTENSION;

// Check for HTTPS
Router::checkHTTPS();

// Session
session_name(COOKIE_PREFIX . 'session');
session_set_cookie_params([
    'path' => '/',
    'secure' => HTTPS,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Autoloader for MODELS
//
// MODELS class naming convention:
//
// 'ExampleClass' looking for in:
// /app/mvc/models/ExampleClass.php
// /app/mvc/models/Example/ExampleClass.php
spl_autoload_register(function($class) {
    if(file_exists(MODELS . $class . EXTENSION)) {
        require_once MODELS . $class . EXTENSION;
    } else {
        // Looking for a folder in MODELS
        $regex = '/(?<=[a-z])(?=[A-Z])/x';
        $classArray = preg_split($regex, $class);
        if(file_exists(MODELS . $classArray[0] . '/' . $class . EXTENSION)) {
            require_once MODELS . $classArray[0] . '/' . $class . EXTENSION;
        }
    }
});
