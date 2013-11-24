<?php
/**
 * MathB
 *
 * This script contains the MathB class that handles request and
 * displays web user interface.
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

use RuntimeException;
use Susam\Pal;


/**
 * Implements the application
 *
 * This class contains methods to process an HTTP request and send the
 * appropriate response.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://mathb.in/5 Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class MathB
{
    /**
     * MathB version
     *
     * @var string
     */
    const VERSION = '0.1';


    /**
     * Configuration object used by the application
     *
     * @var Configuration
     */
    private $conf;


    /**
     * View object used to display each page
     *
     * @var View
     */
    private $view;


    /**
     * Preview object to send preview files
     *
     * @var Preview
     */
    private $preview;


    /**
     * Constructs an instance of MathB
     *
     * If this constructor is invoked without arguments, a default
     * configuration and a default view is used to instantiate this
     * class. The default configuration and the default view may be
     * overridden by specifying the $conf and $view arguments. The $conf
     * argument should be an instance of a subclass of Configuration.
     *
     * @param Configuration $conf Configuration object
     * @param View          $view View object
     *
     * @return void
     */
    public function __construct($conf = null, $view = null)
    {
        date_default_timezone_set('GMT');
        self::splAutoloadRegister(dirname(__DIR__));
        $this->conf = isset($conf) ? $conf : new Configuration();
        $this->view = isset($view) ? $view : new View(); 
        $this->preview = new Preview($this->conf->getCacheDirectoryPath());
        $this->conf->createDirectories();
    }


    /**
     * Processes HTTP request
     *
     * This method processes HTTP GET or HTTP POST request and generates
     * a response to display an appropriate page to respond to this
     * request.
     *
     * @return void
     */
    public function run()
    {
        if (isset($_POST['code']))
            $this->processPostRequest();
        else
            $this->processGetRequest();
    }


    /**
     * Sets the class autoloading function
     *
     * The include path specified while calling this function must be a
     * path to the parent directory of the directory containing this
     * script in order for class autoloading within this project to
     * work.
     *
     * This method appends the specified include path to the existing
     * include path, and then sets spl_autoload as the autoloader.
     *
     * @param string $includePath Path to be appended to the include
     *                            path
     *
     * @return void
     */
    public static function splAutoloadRegister($includePath)
    {
        set_include_path(get_include_path() . PATH_SEPARATOR .
                         $includePath);
        spl_autoload_register();
    }


    /**
     * Processes HTTP GET request when home page or a post is requested
     *
     * Requests to the home page of this application or a particular
     * post occur in the form of HTTP GET requests. These requests
     * originate from visiting URLs of the form http://mathb.in/,
     * http://mathb.in/1, etc. An URL of the form http://mathb.in/1 is
     * rewritten http://mathb.in/?p=1. This method reads the value of
     * the p parameter to obtain the ID of the post requested and
     * displays it. If the p parameter is absent, then the home page is
     * displayed.
     *
     * @return void
     */
    private function processGetRequest()
    {
        // If it's a request for a preview file, send the file and
        // return
        $preview = Pal::get($_GET, 'preview', '');
        if ($preview !== '') {
            $this->preview->sendFile($preview);
            return;
        }

        $id = Pal::get($_GET, 'p', '');
        $key = Pal::get($_GET, 'key', '');

        // If no existing post is requested, display an input page with
        // blank form
        if ($id === '') {
            $this->view->inputPage(new Bag());
            return;
        }

        // If an existing post is requested, attempt to load it from
        // file system
        try {
            $path = $this->conf->getPostFilePath($id);
            $post = Post::newPostFromFile($id, $path);
            $this->preview->cachePost($post);
        } catch (RuntimeException $e) {
            $errorCode = $e->getCode();
            if ($errorCode === Post::NOT_FOUND) {
                http_response_code(404);
                $bag = new Bag();
                $bag->pageTitle = 'Request error';
                $this->view->errorPage($bag, 'This post does not exist.');
            } else {
                http_response_code(500);
                $bag = new Bag();
                $bag->pageTitle = 'System error';
                $this->view->errorPage($bag, $e->getMessage());
            }
            return;
        }

        // Check if the post is enabled
        if ($post->enabled === false) {
            http_response_code(403);
            $bag = new Bag();
            $bag->pageTitle = 'Forbidden';
            $this->view->errorPage($bag, 'This post is disabled.');
            return;
        }

        // Check if there is a match in the key
        if ($post->key !== '' && $key !== $post->key) {
            http_response_code(403);
            $bag = new Bag();
            $bag->pageTitle = 'Forbidden';
            $this->view->errorPage($bag,
                                   'This is a secret post. This post ' .
                                   'cannot be accessed unless a ' .
                                   'valid key is specified in the URL.');
            return;
        }

        // Display post since all checks have passed
        $this->view->inputPage(new Bag($this->conf, $post));
    }


    /**
     * Processes HTTP POST request when a new post is submitted
     *
     * A new post is submitted as an HTTP POST request. This method
     * processes the data submitted in the HTTP POST request, verifies
     * the data, and either displays error messages if an error occurs,
     * or processes the data, saves the post and redirects the user to
     * the URL of the new post.
     *
     * @return void
     */
    private function processPostRequest()
    {
        // Create new post from HTTP POST data
        $post = Post::newPostFromInput();

        // Cache the post to display preview
        try {
            $this->preview->cachePost($post);
        } catch (RuntimeException $e) {
            $this->view->inputPage(new Bag($this->conf, $post),
                                   $e->getMessage());
            return;
        }

        // Validate and display input page again if there are errors
        $inputErrors = $post->validate();
        if (count($inputErrors) > 0) {
            $bag = new Bag($this->conf, $post);
            $this->view->inputPage($bag, $inputErrors);
            return;
        }

        // Check if preview is requested
        if (Pal::get($_POST, 'preview', '') !== '') {
            $this->view->inputPage(new Bag($this->conf, $post));
            return;
        }

        // Check if client is blacklisted
        $ip = $_SERVER['REMOTE_ADDR'];
        if ($this->conf->clientIsBlacklisted($ip)) {
            http_response_code(403);
            $this->view->inputPage(new Bag($this->conf, $post),
                                   'Your IP address (' . $ip .
                                   ') is blacklisted.');
            return;
        }

        // Write the post to the file system
        try {
            $id = $this->incrementCount();
            $post->write($this->conf->getPostFilePath($id));
        } catch (RuntimeException $e) {
            $this->view->inputPage(new Bag($this->conf, $post),
                                   $e->getMessage());
            return;
        }

        // Let the user redirect to the new post with HTTP GET request
        Pal::redirect($this->conf->getPostURL($id, $post->key), 303);
    }


    /**
     * Increments the count in the count file
     *
     * If a count file exists, read the count from it, increment it, and
     * return the new count. If it does not exist, create a count file
     * with a count of 1 and return 1.
     *
     * @return integer Count for new post
     * @throws RuntimeException If this method fails to read from or
     *                          write to the count file
     */
    private function incrementCount()
    {
        // Open count file
        $path = $this->conf->getCountFilePath();
        $file = fopen($path, 'c+');
        if ($file === false)
            throw new RuntimeException(
                    "Could not open $path for writing",
                    Post::WRITE_ERROR);

        // Lock count file
        $success = flock($file, LOCK_EX);
        if ($success === false)
            throw new RuntimeException("Could not lock $path",
                                       Post::WRITE_ERROR);

        // Increment count
        if (file_exists($path) === false || filesize($path) === 0) {
            $id = '1';
        } else {
            $id = fread($file, filesize($path));
            if ($id === false)
                throw new RuntimeException("Could not read $path",
                                           Post::WRITE_ERROR);
            $id = intval($id) + 1;
        }

        // Delete previous count
        $success = ftruncate($file, 0);
        if ($success === false)
            throw new RuntimeException("Could not truncate $path",
                                       Post::WRITE_ERROR);

        // Reset count file
        $success = rewind($file);
        if ($success === false)
            throw new RuntimeException("Could not rewind $path",
                                       Post::WRITE_ERROR);

        // Write new count
        $success = fwrite($file, "$id\n");
        if ($success === false)
            throw new RuntimeException("Could not write to $path",
                                       Post::WRITE_ERROR);

        // Unlock count file
        $success = flock($file, LOCK_UN);
        if ($success === false)
            throw new RuntimeException("Could not unlock $path",
                                       Post::WRITE_ERROR);

        // Close count file
        $success = fclose($file);
        if ($success === false)
            throw new RuntimeException("Could not close $path",
                                       Post::WRITE_ERROR);

        // Return new count, i.e. the ID of the next post
        return $id;
    }
}
