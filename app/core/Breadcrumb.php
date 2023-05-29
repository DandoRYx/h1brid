<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Breadcrumb class
 *
 * Handles adding and loading breadcrumbs.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Breadcrumb {
    private static $breadcrumbs = [];

    /**
     * load
     *
     * Loads breadcrumbs into header.
     *
     * @link https://developers.google.com/search/docs/appearance/structured-data/breadcrumb
     * @return void
     */
    public static function load() : void {
        if(empty(self::$breadcrumbs)) {
            return;
        }

        // Start of code
        echo "<script type=\"application/ld+json\">";
        echo "{";
        echo "\"@context\": \"https://schema.org\",";
        echo "\"@type\": \"BreadcrumbList\",";
        echo "\"itemListElement\": [";

        // Iterate through each breadcrumb
        foreach (self::$breadcrumbs as $position => $data) {
            $data = explode('>', $data);

            // Adding to code
            echo "{";

            echo "\"@type\": \"ListItem\",";
            echo "\"position\": " . $position . ",";
            echo "\"name\": \"" . $data[0] . "\"";
            if($position !== array_key_last(self::$breadcrumbs)) {
                echo ",\"item\": \"" . $data[1] . "\"";
            }

            echo "}";

            // If it is not the last item in the array
            // add comma (,)
            if($position !== array_key_last(self::$breadcrumbs)) {
                echo ',';
            }
        }

        // End of code
        echo "]}</script>" . PHP_EOL;
    }

    /**
     * add
     *
     * Adds breadcrumb to array.
     * Example of controller of Award Winners view
     * Breadcrumb::add(1, 'Books', Language::link('books'));
     * Breadcrumb::add(2, 'Science Fiction', Language::link('sciencefiction'));
     * Breadcrumb::add(3, 'Award Winners', '');
     *
     * Use <ol> and <li> for breadcrumbs.
     * You can leave the last breadcrumb URL empty.
     *
     * @param int $position
     * @param string $name
     * @param string $url
     *
     * @return void
     */
    public static function add(int $position, string $name, string $url = '') : void {
        $absoluteURL = PAGE;
        $absoluteURL = rtrim($absoluteURL, '/');

        $absoluteURL .= $url;

        $data = $name . '>' . $absoluteURL;

        self::$breadcrumbs[$position] = $data;
    }
}
