<?php
/**
 * Utility library of Susam Pal
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
 * Utility class
 *
 * This class contains several utility methods to perform routine tasks.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://susam.in/licenses/bsd/ Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class Pal
{
    /**
     * Removes a file or directory
     *
     * This method deletes a file or a directory. If a directory is
     * specified as the argument to this method, then the directory is
     * deleted recursively, i.e. all files and subdirectories in it are
     * deleted.
     *
     * @param string $name Name of file or directory to be removed
     *
     * @return boolean true on success; false otherwise
     */
    public static function rm($name) {
        if (! file_exists($name)) {
            return false;
        }

        if (is_file($name)) {
            return unlink($name);
        } else if (is_dir($name)) {
            $objects = scandir($name);
            foreach ($objects as $object) {
                if ($object !== '.' && $object !== '..') {
                    $object = "{$name}$object";
                    self::rm($object);
                }
            }
            return rmdir($name);
        }
    }


    /**
     * Returns the scheme name used in the request
     *
     * This method returns 'https' if the script calling this function
     * is requested with HTTPS protocol. It returns 'http' otherwise.
     *
     * @return string Scheme name, i.e. 'http' or 'https'
     */
    public static function getURLScheme()
    {
        if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            return 'https';
        else
            return 'http';
    }


    /**
     * Returns URL to the current host
     *
     * This method returns the URL to the host in which the script
     * calling this function is running.
     *
     * @return string URL to the current host
     */
    public static function getHostURL()
    {
        return self::getURLScheme() . '://' . $_SERVER['HTTP_HOST'] . '/';
    }


    /**
     * Returns URL to the current page
     *
     * This method returns the URL used to request the script calling
     * this function.
     *
     * @return string URL to the current page
     */
    public static function getURL()
    {
        return self::getURLScheme() . '://' .
               $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


    /**
     * Returns HTML code for a hyperlink with the specified URL and text
     *
     * This method returns the HTML code for an anchor element that
     * points to the specified target URL and contains the specified
     * anchor text.
     *
     * If the specified URL begins with a forward slash, i.e. '/', then
     * the target URL used in the anchor text is a complete URL
     * beginning with the URL scheme, i.e. 'http' or 'https'. If the
     * specified URL does not begin with a forward slash, then it used
     * as is in the target URL.
     *
     * @param string $url  Target of the hyperlink
     * @param string $text Anchor text
     *
     * @return string HTML code for anchor element
     */
    public static function link($url, $text = '')
    {
        if ($url[0] === '/')
            $url = self::getURLScheme() . $_SERVER['HTTP_HOST'] . $url;

        if ($text === '')
            $text = $url;

        return '<a href="' . $url . '">' . $text . '</a>';
    }


    /**
     * Redirects the user agent to a new URL
     *
     * This method accepts an optional second argument that specifies
     * the response code with which to redirect the user agent to the
     * new URL. If the response code is not specified, a default
     * response code of 307, i.e. "Temporary Redirect", is assumed.
     *
     * @param string  $url           Redirect URL
     * @param integer $responseCode  HTTP response code
     *
     * @return void
     */
    public static function redirect($url, $responseCode = 307)
    {
        header("Location: $url", true, $responseCode); 
    }


    /**
     * Returns the value for a specified key in an array
     *
     * This method accepts an optional third argument which can be used
     * to specify the value that must be returned if the specified key
     * is not found in the specified array. When the third argument is
     * not specified, it is assumed to be null.
     *
     * @param array $arr     Array
     * @param mixed $key     Key
     * @param mixed $default Default value to be returned if the
     *                               key is not present in the array
     *
     * @return mixed $arr[$key] if it exists; $default otherwise
     */
    public static function get($arr, $key, $default = null)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }


    /**
     * Converts the specified word to its plural form if the specified
     * count is greater than 1.
     *
     * This method simply adds an 's' as a prefix to the word if the
     * specified count exceeds 1. If the count does not exceed 1, the
     * word is returned unchanged.
     *
     * @param string  $word   Word to be converted to plural form if
     *                        the $count > 1
     * @param integer $count  Count to be used to decide if the word is
     *                        plural or not
     *
     * @return string Plural form of the specified word if the specified
     *                count is greater than 1
     */
    public static function plural($word, $count = 2)
    {
        if ($count <= 1)
            return $word;
        else
            return $word . 's';
    }


    /**
     * Returns the HTML code to display a text file
     *
     * The HTML code returned contains certain characters with special
     * significance in HTML converted to HTML entities with
     * htmlspecialchars function.
     *
     * @param string $path Path of the text file
     *
     * @return string HTML representation of the text in the specified
     *                file
     */
    public static function fileToHTML($path)
    {
        $content = trim(file_get_contents($path));
        return htmlspecialchars($content, ENT_COMPAT, 'UTF-8');
    }
}
?>
