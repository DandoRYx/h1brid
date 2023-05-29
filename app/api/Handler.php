<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

namespace Api;

/**
 * Handler class
 *
 * Example API handler class.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Handler {
    /**
     * action
     *
     * Example API action.
     *
     * @return bool
     */
    public static function action() : bool {
        var_dump(\Api::getParameters());
        var_dump(\Api::getKey());
        return true;
    }
}
