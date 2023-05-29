<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Font class
 *
 * Handles loading and generating fonts.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Font {
    private static $styles = [
        'normal',
        'italic',
        'oblique'
    ];

    const DEFAULT_FONT_EXTENSION = 'ttf';

    /**
     * generate
     *
     * Generates fonts file.
     *
     * @return void
     */
    public static function generate() : void {
        header('Content-type: text/css');

        $path = _PUBLIC . 'fonts';
        $files = array_diff(scandir($path), array('.', '..'));

        foreach($files as $fontFile) {
            // Check for font file
            if(pathinfo($fontFile, PATHINFO_EXTENSION) != self::DEFAULT_FONT_EXTENSION) {
                continue;
            }

            // Splitting font path into parts
            $path = basename($fontFile, '.' . self::DEFAULT_FONT_EXTENSION);
            $contents = explode('_', $path);

            // Getting font weight
            $weight = end($contents);
            array_pop($contents);

            // Getting font style
            $style = end($contents);
            array_pop($contents);

            // Check if proper style is used
            if(!in_array($style, self::$styles)) {
                continue;
            }

            // Getting font name
            $name = implode(' ', $contents);

            // Output font into CSS
            echo '@font-face { ';
            echo 'font-family: \'' . $name . '\'; ';
            echo 'src: url("../fonts/' . $fontFile . '"); ';
            echo 'font-style: ' . $style . '; ';
            echo 'font-weight: ' . $weight . '; ';
            echo '}' . PHP_EOL;
        }
    }
}
