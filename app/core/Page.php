<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Page class
 *
 * Handles page.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Page {
    private static $title;
    private static $description;

    /**
     * init
     *
     * Initializes page.
     *
     * @return bool
     */
    public static function init() : bool {
        if(!Language::exists(Language::get())) {
            $message = 'specified language ' . Language::get() . ' does not exist';
            Log::create($message, 'Page::init');
            return false;
        }

        self::$title = Language::getTitle();
        self::$description = Language::getDescription();

        if(self::$title == null) {
            $message = 'title ' . Router::getHandler() . ' in language ' . Language::get() . ' does not exist';
            Log::create($message, 'Page::init');

            self::invalidPage();
            return false;
        }

        if(self::$description == null) {
            $message = 'description ' . Router::getHandler() . ' in language ' . Language::get() . ' does not exist';
            Log::create($message, 'Page::init');

            self::invalidPage();
            return false;
        }

        self::$title = TITLE_PREFIX . self::$title;

        return true;
    }

    /**
     * setCustomTitle
     *
     * Sets custom title.
     *
     * @param string $title
     * @param bool $usePrefix
     *
     * @return void
     */
    public static function setCustomTitle(string $title, bool $usePrefix = false) : void {
        self::$title = $title;
        if($usePrefix) {
            self::$title = TITLE_PREFIX . self::$title;
        }
    }

    /**
     * setCustomDescription
     *
     * Sets custom description.
     *
     * @param string $description
     *
     * @return void
     */
    public static function setCustomDescription(string $description) : void {
        self::$description = $description;
    }

    /**
     * invalidPage
     *
     * Sets title and description to error.
     * Also routes to 404.
     *
     * @return void
     */
    public static function invalidPage() : void {
        self::$title = Language::getTitle('error');
        self::$description = Language::Description('error');
        Router::to404();
    }

    /**
     * getComponent
     *
     * Gets component from
     * /app/mvc/controllers/components/
     * and
     * /app/mvc/views/components/
     *
     * @param string $component
     * @param array $arguments
     *
     * @return bool
     */
    public static function getComponent(string $component, array $arguments = []) : bool {
        if(!file_exists(VIEWS . 'components/' . $component . EXTENSION)) {
            return false;
        }

        if(file_exists(CONTROLLERS . 'components/' . $component . EXTENSION)) {
            include CONTROLLERS . 'components/' . $component . EXTENSION;
        }

        include VIEWS . 'components/' . $component . EXTENSION;
        return true;
    }

    /**
     * loadLogo
     *
     * Loads logo into <head> for SEO.
     *
     * @param string $file
     *
     * @return bool
     */
    public static function loadLogo(string $file) : bool {
        if(!file_exists(_PUBLIC . 'images/' . $file)) {
            return false;
        }

        if(filesize(_PUBLIC . 'images/' . $file) == 7772) {
            return false;
        }

        echo "<script type=\"application/ld+json\">";
        echo "{";
        echo "\"@context\": \"https://schema.org\",";
        echo "\"@type\": \"Organization\",";
        echo "\"url\": \"" . PAGE . "\",";

        $path = PAGE;
        if(!str_ends_with($path, '/')) {
            $path .= '/';
        }

        $path .= 'resource/images/' . $file;
        echo "\"logo\": \"" . $path . "\"";

        echo "}";
        echo "</script>" . PHP_EOL;

        return true;

    }

    /**
     * getTitle
     *
     * Gets title.
     *
     * @return string|null
     */
    public static function getTitle() : ?string {
        return self::$title;
    }

    /**
     * getDescription
     *
     * Gets description.
     *
     * @return string|null
     */
    public static function getDescription() : ?string {
        return self::$description;
    }
}
