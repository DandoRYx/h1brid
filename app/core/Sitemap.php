<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Sitemap class
 *
 * Handles loading sitemap.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Sitemap {
    private static $sitemap = [];

    /**
     * generate
     *
     * Generates sitemap from array.
     *
     * @link developers.google.com/search/docs/specialty/international/localized-versions
     * @return void
     */
    public static function generate() : void {
        // Include sitemap
        include_once _APP . 'sitemap' . EXTENSION;

        // XML format
        header("Content-Type: application/xml; charset=utf-8");
        echo '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
        echo '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"  xmlns:xhtml="https://www.w3.org/1999/xhtml">' . PHP_EOL;

        // Loop through all links
        foreach(self::$sitemap as $sitemapLink) {
            if(!is_array($sitemapLink)) {
                // Normal link
                echo '<url>'. PHP_EOL;
                echo '<loc>' . $sitemapLink . '</loc>' . PHP_EOL;
                echo '</url>' . PHP_EOL;
            } else {
                // Multi language link array
                $sitemapArray = $sitemapLink;
                $languages = array_keys($sitemapArray);

                foreach($languages as $linkLanguage) {
                    echo '<url>' . PHP_EOL;
                    echo '<loc>' . $sitemapArray[$linkLanguage] . '</loc>' . PHP_EOL;

                    foreach($sitemapArray as $language => $link) {
                        // Add alternate language links
                        echo '<xhtml:link
                                rel="alternate"
                                hreflang="' . $language . '"
                                href="' . $link . '" />' . PHP_EOL;
                    }

                    echo '</url>' . PHP_EOL;
                }
            }
        }

        echo '</urlset>';
    }

    /**
     * add
     *
     * Adds link to sitemap array.
     * Example:
     * Sitemap::add(PAGE);
     *
     * Example for multilanguage pages:
     * Sitemap::add(
     *     array(
     *         'en' => PAGE . 'en/',
     *         'de' => PAGE . 'de/'
     *     )
     * );
     *
     * @param string $link
     *
     * @return void
     */
    public static function add(string $link) : void {
        self::$sitemap[] = $link;
    }
}
