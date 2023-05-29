<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Cookie class
 *
 * Handles creating and deleting cookies.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Cookie {

    /**
     * create
     *
     * Creates cookie.
     *
     * @param string $name
     * @param string $value
     * @param int $time
     *
     * @return void
     */
    public static function create(string $name, string $value, int $time = null) : void {
        $maxAge = '';

        // If the cookie has set time
        if($time != null) {
            $maxAge = 'Max-Age=' . $time . ';';
        }

        // Set cookie
        header('Set-Cookie: ' .
                $name .
                '=' .
                $value .
                '; ' .
                $maxAge .
                'Expires=' .
                COOKIE_INFINITE .
                '; HttpOnly; Path=/; SameSite=Lax;', false);

    }

    /**
     * delete
     *
     * Deletes cookie.
     *
     * @param string $name
     *
     * @return void
     */
    public static function delete(string $name) : void {
        unset($_COOKIE[$name]);
        header('Set-Cookie: ' . $name . '=NULL; Max-Age=-1; HttpOnly; Path=/;', false);
    }

}
