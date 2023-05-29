<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

require '../config.php';

// Routing
Router::init();
Language::init();
Page::init();

$parameters = Router::getParameters();
if(Router::getMode() == 'view') {
    // Controller
    if(file_exists(CONTROLLERS . Router::getHandler() . EXTENSION)) {
        require CONTROLLERS . Router::getHandler() . EXTENSION;
    }

    // View
    require VIEWS . 'components/header' . EXTENSION;
    require VIEWS . Router::getHandler() . EXTENSION;
    require VIEWS . 'components/footer' . EXTENSION;
} else if(Router::getMode() == 'data') {
    require DATA . Router::getHandler() . EXTENSION;
}

// Be safe
exit;
