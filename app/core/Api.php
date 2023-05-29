<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Api class
 *
 * Handles API loading and calls.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Api {
    private static $apis = [];

    private static $handler;
    private static $action;
    private static $parameters = [];
    private static $key;

    /**
     * init
     *
     * Initializes API.
     *
     * @return void
     */
    public static function init() : void {
        require _APP . 'api' . EXTENSION;
    }

    /**
     * add
     *
     * Adds API action into array.
     *
     * @param string $handler
     * @param string $action
     *
     * @return void
     */
    private static function add(string $handler, string $action) : void {
        if(array_key_exists($handler, self::$apis)) {
            $handlerArray = self::$apis[$handler];
            $newHandlerArray = [$action];

            $handlerArray = array_merge($handlerArray, $newHandlerArray);
            self::$apis[$handler] = $handlerArray;
        } else {
            self::$apis[$handler] = [$action];
        }
    }

    /**
     * load
     *
     * Loads API handler and action.
     *
     * @param string $handler
     * @param string $action
     * @param array $parameters
     *
     * @return bool
     */
    public static function load(string $handler, string $action, array $parameters) : bool {
        self::$handler = strtolower($handler);
        self::$handler = ucfirst(self::$handler);
        self::$action = strtolower($action);

        if(!self::exists(self::$handler, self::$action)) {
            return false;
        }

        // Handler
        if(!file_exists(_APP . 'api/' . self::$handler . EXTENSION)) {
            return false;
        }

        require _APP . 'api/' . self::$handler . EXTENSION;
        if(!class_exists('Api\\' . self::$handler)) {
            return false;
        }

        // Action
        if(!method_exists('Api\\' . self::$handler, self::$action)) {
            return false;
        }

        // Parameters
        self::$parameters = $parameters;

        // Key
        self::$key = null;
        if(isset($_POST['X-API-KEY'])) {
            self::$key = $_POST['X-API-KEY'];
        }

        return true;
    }

    /**
     * call
     *
     * Calls API action.
     *
     * @return bool
     */
    public static function call() : bool {
        // Need to use variable names because PHP does
        // not allow class properties for method calling
        $handler = self::$handler;
        $action = self::$action;

        $return = ('Api\\' . $handler)::$action();
        return ($return == null) ? true : $return;
    }

    /**
     * exists
     *
     * Checks if API handler and action exists.
     *
     * @param string $handler
     * @param string $action
     *
     * @return bool
     */
    public static function exists(string $handler, string $action) : bool {
        if(!array_key_exists($handler, self::$apis)) {
            return false;
        }

        return in_array($action, self::$apis[$handler]);
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

    /**
     * getKey
     *
     * Gets key.
     *
     * @return string|null
     */
    public static function getKey() : ?string {
        return self::$key;
    }
}
