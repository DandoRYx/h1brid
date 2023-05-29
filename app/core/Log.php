<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Log class
 *
 * Handles creating logs.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Log {
    /**
     * create
     *
     * Writes log into LOG file.
     *
     * @param string $message
     * @param string $process
     *
     * @return bool
     */
    public static function create(string $message, string $process = 'unspecified') : bool {
        $time = (new DateTime('now'))->format('Y-m-d H:i:s');
        $url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

        $log = $time;
        $log .= ' \'';
        $log .= $url;
        $log .= '\' ';
        $log .= $process;
        $log .= ': ';
        $log .= $message;
        $log .= PHP_EOL;

        // Write into the file
        $file = fopen(LOG, 'a');
        fwrite($file, $log);
        fclose($file);

        return true;
    }
}
