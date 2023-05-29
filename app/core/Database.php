<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Database class
 *
 * Handles connection to the database.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Database {
    private static $handle;

    private static $options = [
        PDO::ATTR_ERRMODE => ((LOCALHOST) ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT),
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    const DEFAULT_CHARSET = 'utf8mb4';

    /**
     * get
     *
     * Gets database connection.
     *
     * @return PDO|null
     */
    public static function get() : ?PDO {
        if(!self::$handle) {
            if(!self::connect()) {
                return null;
            }
        }

        return self::$handle;
    }

    /**
     * connect
     *
     * Connects to the database.
     *
     * @return bool
     */
    private static function connect() : bool {
        try {
            $data = '';
            $data .= 'mysql:host=' . ((LOCALHOST) ? DB_HOST_LOCALHOST : DB_HOST) . ';';
            $data .= 'dbname=' . ((LOCALHOST) ? DB_NAME_LOCALHOST  : DB_NAME) . ';';
            $data .= 'charset=' . self::DEFAULT_CHARSET;

            $user = (LOCALHOST) ? DB_USER_LOCALHOST : DB_USER;
            $pass = (LOCALHOST) ? DB_PASS_LOCALHOST : DB_PASS;

            self::$handle = new PDO($data, $user, $pass, self::$options);
            return true;
        } catch(PDOException $e) {
            // Exception
            Log::create($e->getMessage(), 'Database::connect');
            if(LOCALHOST) {
                die($e->getMessage());
            }

            return false;
        }
    }

    /**
     * getLastInsertId
     *
     * Gets last inserted row ID.
     *
     * @param string|null $name
     *
     * @return string|null
     */
    public static function getLastInsertId(?string $name = null) : ?string {
        if(!self::$handle) {
            Log::create('not utilized database handle', 'Database::getLastInsertId');
            return null;
        }

        $id = self::$handle->lastInsertId($name);

        // In case of false return null
        if($id === false) {
            return null;
        }

        return $id;
    }
}
