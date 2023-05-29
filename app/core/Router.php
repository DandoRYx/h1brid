<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Router class
 *
 * Handles routing of the website.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Router {
    private static $url = [];
    private static $mode;
    private static $handler;
    private static $parameters = [];

    private static $modes = [
        'view',
        'data',
        'api',
        'resource'
    ];

    private static $individual = [
        'sitemap.xml',
        'humans.txt',
        'robots.txt',
        'favicon.ico',
        'apple-touch-icon.png'
    ];

    private static $sentLocationHeader = false;

    const DEFAULT_MODE = 'view';

    /**
     * init
     *
     * Initializes router.
     *
     * @return void
     */
    public static function init() : void {
        // URL
        self::$url = self::getURL();

        // Getting data (Language or Mode and Handler)
        $data = self::$url[0];
        self::shiftURL();
        if(in_array($data, self::$modes) && $data != self::DEFAULT_MODE) {
            self::$mode = $data;

            if($data == 'data') {
                // Language for data
                if(isset($_COOKIE[COOKIE_PREFIX . 'language'])) {
                    Language::set($_COOKIE[COOKIE_PREFIX . 'language']);
                } else {
                    Language::set(DEFAULT_LANGUAGE);
                }
            } else if($data == 'api') {
                // Language for API
                Language::set(DEFAULT_LANGUAGE);

                $handler = self::$url[0];
                self::shiftURL();
                $action = self::$url[0];
                self::shiftURL();

                // Parameters
                $parameters = self::$url;
                if($parameters == ['/']) {
                    $parameters = [];
                }

                Api::init();
                if(Api::load($handler, $action, $parameters)) {
                    if(Api::call()) {
                        exit;
                    }
                }

                self::to404();
                return;
            } else {
                self::resource();
                return;
            }

            // Getting the handler
            self::$handler = self::$url[0];
            self::shiftURL();
        } else {
            if(in_array($data, self::$individual)) {
                // Individual routes (resources)
                self::resource($data);
                return;
            } else {
                // View
                self::$mode = self::DEFAULT_MODE;
                if(!MULTI_LANGUAGE) {
                    Language::set(DEFAULT_LANGUAGE);
                    self::$handler = $data;
                } else {
                    Language::set($data);
                    self::$handler = self::$url[0];
                    self::shiftURL();
                }
            }
        }

        // Create language cookie
        Cookie::create(COOKIE_PREFIX . 'language', Language::get());

        // Parameters
        self::$parameters = self::$url;
        if(self::$parameters == ['/']) {
            self::$parameters = [];
        }

        // View redirect
        // MAIN_VIEW -> '/'
        if(self::$handler == MAIN_VIEW) {
            // Allow /MAIN_VIEW/ with parameters
            if(empty(self::$parameters)) {
                self::toHome();
            }
        }

        // Main view
        if(self::$handler == '/') {
            self::$handler = MAIN_VIEW;
        }

        // 404
        if((self::$mode == 'view' && !file_exists(VIEWS . self::$handler . EXTENSION))
           ||
           (self::$mode == 'view' && self::$handler == 'error')
           ||
           (self::$mode == 'data' && !file_exists(DATA . self::$handler . EXTENSION))) {
            self::to404();
        }
    }

    /**
     * resource
     *
     * Loads resource.
     *
     * @param string $individual
     *
     * @return void
     */
    private static function resource(string $individual = null) : void {
        if($individual == 'sitemap.xml') {
            Sitemap::generate();
            exit;
        }

        $folders = [
            'images',
            'fonts',
            'css',
            'js'
        ];

        $folder = '';
        $file = $individual;
        if($individual == NULL) {
            // If $individual is not specified
            $folder = self::$url[0];
            if(!in_array($folder, $folders)) {
                self::to404();
                return;
            }

            $folder .= '/';
            self::shiftURL();

            $file = self::$url[0];
            self::shiftURL();

            // /resources/css/fonts.css
            if($folder == 'css/' && $file == 'fonts.css') {
                Font::generate();
                exit;
            }
        }

        $path = _PUBLIC . $folder . $file;

        // Invalid file
        if(!file_exists($path) || $file == '/') {
            self::to404();
            return;
        }

        $mime = self::getMIME($path);

        header('Content-Type: ' . $mime);
        readfile($path);
        exit;
    }

    /**
     * getMIME
     *
     * Gets MIME type of specified file.
     *
     * @param string $file
     *
     * @return string
     */
    private static function getMIME(string $file) : string {
        $extensions = [
            'css' => 'text/css',
            'js' => 'text/javascript',
            'ini' => 'application/textedit',
            'diff' => 'text/plain',
            'sql' => 'application/sql',
            'rst' => 'text/x-rst'
        ];

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if(array_key_exists($extension, $extensions)) {
            return $extensions[$extension];
        }

        $mime = mime_content_type($file);

        if($mime === false) {
            return 'application/octet-stream';
        }

        return $mime;
    }

    /**
     * getURL
     *
     * Gets URL fields.
     *
     * @return array
     */
    private static function getURL() : array {
        $url;

        // There is no need for query, because query should not be used,
        // and if it is, then it is accessable by $_GET[]. Therefore we
        // remove the query from the URL because we just need the path.
        if(isset($_SERVER['REQUEST_URI'])) {
            $url = strtok($_SERVER['REQUEST_URI'], '?');
            if($url === false) {
                $url = $_SERVER['REQUEST_URI'];
            }
        } else {
            $url = $_SERVER['PATH_INFO'];
        }

        // Slashes for SEO
        $needToRedirect = false;

        // Multiple slashes in a row
        // Not using str_contains to support PHP version lower than 8
        // even though you SHOULD use PHP 8.
        if(strpos($url, '//') !== false) {
            $url = preg_replace('~/+~', '/', $url);
            $needToRedirect = true;
        }

        // Trailing slashes
        if($url != '/') {
            if(mb_substr($url, -1) == '/') {
                if(!TRAILING_SLASH) {
                    $url = mb_substr($url, 0, strlen($url) - 1);
                    $needToRedirect = true;
                }
            } else {
                if(TRAILING_SLASH) {
                    $url = $url . '/';
                    $needToRedirect = true;
                }
            }
        }

        // 301 Redirect if faulty
        if($needToRedirect) {
            self::toPermanent($url);
        }

        // Put URL into array
        $arrayURL = [];
        if(empty($url) || $url == '/') {
            $arrayURL = ['/'];
        } else {
            $arrayURL = explode('/', ltrim($url, '/'));
        }

        return $arrayURL;
    }

    /**
     * shiftURL
     *
     * Shifts URL array.
     *
     * @return void
     */
    private static function shiftURL() : void {
        array_shift(self::$url);

        if(!isset(self::$url[0]) || empty(self::$url[0])) {
            self::$url = ['/'];
        }
    }

    /**
     * redirect
     *
     * Redirects page to specified URL.
     *
     * @param string $url
     *
     * @return void
     */
    public static function redirect(string $url) : void {
        header('location: ' . $url);
        exit;
    }

    /**
     * toHome
     *
     * 301 home redirect.
     *
     * @return void
     */
    public static function toHome() : void {
        header('HTTP/1.1 301 Moved Permanently');

        // No cache for LOCALHOST
        // better for testing and development
        if(LOCALHOST) {
            header('Cache-Control: no-cache');
        }

        if(!MULTI_LANGUAGE) {
            header('location: /');
        } else {
            if(self::$language == '/') {
                header('location: /' . DEFAULT_LANGUAGE);
            } else {
                header('location: /' . Language::get());
            }
        }

        exit;
    }

    /**
     * to404
     *
     * 404 error redirect.
     *
     * @return void
     */
    public static function to404() : void {
        if(self::$sentLocationHeader == false) {
            header('HTTP/1.0 404 Not Found');
            self::$sentLocationHeader = true;
        }

        self::$mode = 'view';
        self::$handler = 'error';

        // Language
        if(Language::get() == NULL) {
            if(isset($_COOKIE[COOKIE_PREFIX . 'language'])) {
                Language::set($_COOKIE[COOKIE_PREFIX . 'language']);
            } else {
                Language::set(DEFAULT_LANGUAGE);

                Cookie::create(COOKIE_PREFIX . 'language', Language::get());
            }
        }
    }

    /**
     * toPermanent
     *
     * Permanently redirects to specified URL.
     *
     * @param string $url
     */
    public static function toPermanent(string $url) {
        header('HTTP/1.1 301 Moved Permanently');
        header('location: ' . $url);
        exit;
    }

    /**
     * checkHTTPS
     *
     * Checks if connecting to web using HTTPS.
     *
     * @return void
     */
    public static function checkHTTPS() : void {
        $needToRedirect = false;

        if(HTTPS && !LOCALHOST) {
            if(isset($_SERVER['HTTPS'])) {
                if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
                    $needToRedirect = true;
                }
            } else {
                if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                    if($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'http') {
                        $needToRedirect = true;
                    }
                } else if(isset($_SERVER['SERVER_PORT'])) {
                    if($_SERVER['SERVER_PORT'] !== 443) {
                        $needToRedirect = true;
                    }
                }
            }
        }

        if($needToRedirect === true) {
            $location = 'https://'  . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            self::toPermanent($location);
        }
    }

    /**
     * getMode
     *
     * Gets mode.
     *
     * @return string
     */
    public static function getMode() : string {
        return self::$mode;
    }

    /**
     * getHandler
     *
     * Gets handler.
     *
     * @return string
     */
    public static function getHandler() : string {
        return self::$handler;
    }

    /**
     * getParameters
     *
     * Gets parameters.
     *
     * @return array
     */
    public static function getParameters() : array {
        return self::$parameters;
    }
}
