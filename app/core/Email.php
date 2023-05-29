<?php

/**
 *
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Ladislav Mihalik <dandoryx@gmail.com>
 *
 */

/**
 * Email class
 *
 * Sending emails.
 *
 * @author Ladislav Mihalik <dandoryx@gmail.com>
 */
class Email {
    private static $name;
    private static $address;

    const BOUNDARY_PREFIX = "H1BRID";
    const DEFAULT_CONTENT_TYPE = "application/octet-stream";

    /**
     * send
     *
     * Sends email.
     *
     * @param string $name
     * @param string $address
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param mixed $attachments
     *
     * @return bool
     */
    public static function send(string $name,
                                string $address,
                                string $to,
                                string $subject,
                                string $message,
                                $attachments = NULL) : bool {
        self::$name = $name;
        self::$address = $address;

        $body = "";
        $headers = "";
        $additionalHeaders = "";

        if($attachments == NULL) {
            // No attachment
            // Headers
            $headers .= "MIME-Version: 1.0" . PHP_EOL;
            $headers .= "Content-Type: text/html; charset=\"UTF-8\"" . PHP_EOL;

            // Message
            $body .= $message . PHP_EOL;
        } else {
            // Attachment(s)
            // MIME Boundary
            $boundary = "=" . self::BOUNDARY_PREFIX . "x" . bin2hex(random_bytes(16)) . "x";

            // Headers
            $headers .= "MIME-Version: 1.0" . PHP_EOL;
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"" . PHP_EOL;

            // Message
            $body .= "--" . $boundary . PHP_EOL;
            $body .= "Content-Type: text/html; charset=\"UTF-8\"" . PHP_EOL;
            $body .= "Content-Transfer-Encoding: 8bit" . PHP_EOL . PHP_EOL;
            $body .= $message . PHP_EOL;

            // If only one attachment
            if(!is_array($attachments)) {
                // Convert $attachments into array
                $attachment = $attachments;
                $attachments = [];
                $attachments[] = $attachment;
                unset($attachment);
            }

            // Loop through all attachments
            foreach ($attachments as $filePath) {
                // Path
                $path = ROOT . $filePath;

                // File info
                $name = basename($filePath);
                $type = mime_content_type($path);
                if($type === false) {
                    $type = self::DEFAULT_CONTENT_TYPE;
                }

                // File content
                $attachment = chunk_split(base64_encode(file_get_contents($path)));

                // Adding to body
                $body .= "--" . $boundary . PHP_EOL;
                $body .= "Content-Type: " . $type . "; name=\"" . $name . "\"" . PHP_EOL;
                $body .= "Content-Transfer-Encoding: base64" . PHP_EOL;
                $body .= "Content-Disposition: attachment" . PHP_EOL . PHP_EOL;
                $body .= $attachment . PHP_EOL . PHP_EOL;
            }

            // End of body
            $body .= "--" . $boundary . "--";
        }

        // Additional headers
        $additionalHeaders .= "From: " . self::$name . " <" . self::$address . ">" . PHP_EOL;

        // Merge headers
        $headers = $additionalHeaders . $headers;

        // Send mail
        $ret = mail($to, $subject, $body, $headers);

        // If failed to prepare email for delivery
        if($ret === false) {
            // Create log message
            $log = 'sending mail failed to ' . $to . ' subject: ' . $subject;
            Log::create($log, 'Email::send');
        }

        return $ret;
    }
}
