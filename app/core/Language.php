<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Language class
 *
 * Handles languages.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Language {
    private static $language;
    private static $path;

    private static $words = [];

    /**
     * init
     *
     * Initializes language.
     *
     * @return void
     */
    public static function init() : void {
        self::$path = LANGUAGES . self::$language . EXTENSION;

        if(!file_exists(self::$path)) {
            if(self::$language == '/' || self::$language == NULL) {
                // No language specified
                Router::toHome();
            } else {
                // Language does not exist
                Router::to404();
                self::$language = DEFAULT_LANGUAGE;
                self::$path = LANGUAGES . DEFAULT_LANGUAGE . EXTENSION;
            }
        }

        // Load language words
        $words = [];
        require self::$path;
        self::$words = $words;
    }

    /**
     * set
     *
     * Sets language.
     *
     * @param string $language
     *
     * @return void
     */
    public static function set(string $language) : void {
        self::$language = $language;
    }

    /**
     * get
     *
     * Gets language.
     *
     * @return string|null
     */
    public static function get() : ?string {
        if(!self::$language) {
            return null;
        }

        return self::$language;
    }

    /**
     * word
     *
     * Gets language word from language array.
     *
     * @param string $key
     *
     * @return string
     */
    public static function word(string $key) : string {
        if(!isset(self::$words[$key])) {
            return '';
        }

        return self::$words[$key];
    }

    /**
     * link
     *
     * Makes relative language URL.
     *
     * @param string $path
     *
     * @return string
     */
    public static function link(string $path) : string {
        if($path[0] != '/') {
            $path = '/' . $path;
        }

        if(!MULTI_LANGUAGE) {
            return $path;
        }

        $language;
        if(!self::$language || self::$language == null) {
            if(isset($_COOKIE[COOKIE_PREFIX . 'language'])) {
                $language = $_COOKIE[COOKIE_PREFIX . 'language'];
            } else {
                $language = DEFAULT_LANGUAGE;
            }
        } else {
            $language = self::$language;
        }

        $url = '/' . $language . $path;
        return $url;
    }

    /**
     * exists
     *
     * Checks if specified language exists.
     *
     * @param string $language
     *
     * @return bool
     */
    public static function exists(string $language) : bool {
        if($language == '/') {
            return false;
        }

        if(!file_exists(LANGUAGES . $language . EXTENSION)) {
            return false;
        }

        if(!file_exists(LANGUAGES . 'titles/' . $language . EXTENSION)) {
            return false;
        }

        if(!file_exists(LANGUAGES . 'descriptions/' . $language . EXTENSION)) {
            return false;
        }

        return true;
    }

    /**
     * getTitle
     *
     * Gets page title by language.
     *
     * @return string|null
     */
    public static function getTitle(?string $handler = null) : ?string {
        $titles = [];
        require LANGUAGES . 'titles/' . self::$language . EXTENSION;

        if($handler == null) {
            if(isset($titles[Router::getHandler()])) {
                return $titles[Router::getHandler()];
            }
        } else {
            if(isset($titles[$handler])) {
                return $titles[$handler];
            }
        }

        return null;
    }

    /**
     * getDescription
     *
     * Gets page description by language.
     *
     * @return string|null
     */
    public static function getDescription(?string $handler = null) : ?string {
        $descriptions = [];
        require LANGUAGES . 'descriptions/' . self::$language . EXTENSION;

        if($handler == null) {
            if(isset($descriptions[Router::getHandler()])) {
                return $descriptions[Router::getHandler()];
            }
        } else {
            if(isset($descriptions[$handler])) {
                return $descriptions[$handler];
            }
        }

        return null;
    }
}
