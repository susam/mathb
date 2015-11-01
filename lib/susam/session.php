<?php
/**
 * Session library
 *
 * This script contains a class that offers several utility methods to
 * perform routine tasks.
 *
 * SIMPLIFIED BSD LICENSE
 * ----------------------
 *
 * Copyright (c) 2012-2013 Susam Pal
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 
 *   1. Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *   2. Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in
 *      the documentation and/or other materials provided with the
 *      distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://susam.in/licenses/bsd/ Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */

namespace Susam;

/**
 * Session class
 *
 * This class contains several utility methods to perform routine tasks.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://susam.in/licenses/bsd/ Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class Session
{
    /**
     * Prefix of the log file path
     *
     * The current date and '.log' extension name is added automatically
     * to this prefix to determine the complete log file path of the log
     * file where this class writes logs to.
     *
     * @var string
     */
    private static $logPathPrefix;


    /**
     * Logging time zone
     *
     * The timestamps logged in the log file is the current time at this
     * time zone.
     *
     * @var string
     */
    private static $timezone;


    /**
     * Time at which session was started expressed as Unix timestamp
     *
     * @var float
     */
    private static $hitTime;


    /**
     * A unique identifier for the client of this request
     *
     * @var string
     */
    private static $client;


    /**
     * Begins a session
     *
     * This method sets cookies for the clients that can be used to log
     * client usage at the end of the session.
     *
     * @param string $logPathPrefix Prefix of log file path
     * @param string $timezone      Time zone to be used while logging
     *
     * @return void
     */
    function beginSession($logPathPrefix, $timezone = 'GMT')
    {
        self::$hitTime = microtime(true);

        self::$logPathPrefix = $logPathPrefix;
        self::$timezone = $timezone;

        $expiry = time() + 86400 * 365 * 5;

        $count = Pal::get($_COOKIE, 'total_hits', 0) + 1;
        setcookie('total_hits', $count, $expiry, '/');

        $count = Pal::get($_COOKIE, 'current_hits', 0) + 1;
        setcookie('current_hits', $count, 0, '/');

        self::$client = Pal::get($_COOKIE, 'client', '');
        if (self::$client === '') {
            self::$client = strval(mt_rand());
        }
        setcookie('client', self::$client, $expiry, '/');
    }


    /**
     * Ends the current session
     *
     * This method writes a line of log to the log file. The log
     * contains request and client details.
     *
     * @return void
     */
    function endSession()
    {
        date_default_timezone_set(self::$timezone);

        $timestamp = date('Y-m-d H:i:s');
        $elapsedTime = (microtime(true) - self::$hitTime) * 1000;
        $elapsedTime = sprintf("%07.3f", $elapsedTime);

        $query = '';
        $sep = '';
        foreach ($_GET as $key => $val) {
            $query .= "$sep$key: $val";
            if ($sep === '')
                $sep = '; ';
        }

        $cookie = '';
        $sep = '';
        foreach ($_COOKIE as $key => $val) {
            if (strpos($key, '__ut') === 0) {
                continue;
            }
            $cookie .= "$sep$key: $val";
            if ($sep == '') {
                $sep = '; ';
            }
        }

        $code = http_response_code();
        foreach (headers_list() as $header_line) {
            if (strpos($header_line, 'Location:') === 0) {
                $code .= ' ' . $header_line;
                break;
            }
        }

        $log = $timestamp . ' - ' .
               $_SERVER['REQUEST_METHOD'] . ' - ' . 
               $code . ' - ' .
               $_SERVER['REMOTE_ADDR'] . ' - ' . 
               Pal::getURL() .  ' - ' .
               $_SERVER['SCRIPT_NAME'] . ' - ' .
               Pal::get($_SERVER, 'HTTP_REFERER', '(noref)') . ' - ' .
               $query . ' - ' .
               $cookie . ' - ' .
               $_SERVER['HTTP_USER_AGENT'] . ' - ' .
               $_SERVER['SERVER_PROTOCOL'] . ' - ' .
               $elapsedTime . "\n";

        $logPath = self::$logPathPrefix . date('Y-m-d') . '.log';

        $file = fopen($logPath, 'a');
        flock($file, LOCK_EX);
        fwrite($file, $log);
        flock($file, LOCK_UN);
    }
}
?>
