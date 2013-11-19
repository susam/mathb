<?php
/**
 * MathB configuration
 *
 * This script contains the Configuration class that contains the
 * definition of the configuration for this application.
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
 * @license http://mathb.in/5 Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */


namespace MathB;

use Susam\Pal;


/**
 * Defines the configuration for the application
 *
 * This class contains various public properties that contain various
 * runtime values used by the application to configure itself and
 * process requests.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://mathb.in/5 Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class Configuration
{
    /**
     * The directory containing the files for all posts.
     *
     * @var string
     */
    public $dataDirectory;


    /**
     * An array of regular expressions to blacklist IP addresses
     *
     * @var array
     */
    public $ipBlacklist;


    /**
     * Constructs an instance of this class with default properties
     *
     * This constructor initializes the properties of this class to
     * default values.
     */
    public function __construct()
    {
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $this->dataDirectory = "$docRoot../mathb-data/";
        $this->ipBlacklist = array();
    }


    /**
     * Returns the path of a post in the file system
     *
     * This method returns the path of a post with the specified ID in
     * the file system.
     *
     * @param string $id ID of the post
     *
     * @return string Path of the post on the file system
     */
    public function getPostFilePath($id)
    {
        return $this->dataDirectory . $id . '.txt';
    }


    /**
     * Returns the path of the count file
     *
     * This method returns the path of the file that maintains the total
     * number of posts.
     *
     * @return string Path of the count file on the file system
     */
    public function getCountFilePath()
    {
        return $this->dataDirectory . 'count.dat';
    }


    /**
     * Returns URL to the post with specified ID and key
     *
     * If the key is specified, then the URL contains a 'key' query
     * parameter; otherwise it does not contain a 'key' query parameter.
     *
     * @param string $id  ID of the post
     * @param string $key Secret key of the post
     *
     * @return URL of the post with the specified ID and key
     */
    public function getPostURL($id, $key = '')
    {
        $url = Pal::getHostURL() . $id;
        if ($key !== '') {
            $url .= '?key=' . $key;
        }
        return $url;
    }


    /**
     * Returns true if and only if the client is blacklisted
     *
     * This method checks whether the specified IP address matches one
     * of the regular expressions specified in $this->ipBlacklist. If it
     * matches, then true is returned; otherwise false is returned.
     *
     * @param string $ip IP address of the client
     *
     * @return boolean true if IP address is blacklisted;
     *                 false otherwise
     */
    public function clientIsBlacklisted($ip)
    {
        foreach ($this->ipBlacklist as $pattern)
            if (preg_match($pattern, $ip) === 1)
                return true;
        return false;
    }
}
