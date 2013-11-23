<?php
/**
 * MathB post
 *
 * This script contains a class to represent a post.
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
 * Represents a post in MathB
 *
 * An instance of this class is used to contain the information in a
 * MathB post.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://mathb.in/5 Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class Post
{
    const NOT_FOUND = 1;
    const WRITE_ERROR = 2;
    const READ_ERROR = 3;

    /**
     * ID of this post
     *
     * @var string
     */
    public $id;


    /**
     * Creation date of this post
     *
     * @var string
     */
    public $date;


    /**
     * Title of this post
     *
     * @var string
     */
    public $title;


    /**
     * Name of the author of this post
     *
     * @var string
     */
    public $name;


    /**
     * Parent post of this post
     *
     * @var string
     */
    public $parent;


    /**
     * Secret key required to access this post
     *
     * @var string
     */
    public $key;


    /**
     * Whether this post is enabled for access or not
     *
     * @var boolean true if this post is enabled; false otherwise
     */
    public $enabled;


    /**
     * Code in this post
     *
     * @var string
     */
    public $code;


    /**
     * Constructs an instance of this class
     *
     * This constructor initializes an instance of this class with
     * default values for every property. All string properties are
     * initialized to empty strings, i.e. zero length strings. The
     * boolean enabled property is initialized to true.
     */
    private function __construct()
    {
        $this->id = '';
        $this->date = '';
        $this->title = '';
        $this->name = '';
        $this->parent = '';
        $this->key = '';
        $this->enabled = true;
        $this->code = '';
    }


    /**
     * Constructs a new instance of an empty post
     *
     * This factory method returns a new instance of this class with its
     * properties initialized to default values. All string properties
     * are initialized to empty strings, i.e. zero length strings. The
     * boolean enabled property is initialized to true.
     *
     * @return Post New empty post
     */
    public static function newEmptyPost()
    {
        return new Post();
    }


    /**
     * Constructs a new instance of this class from HTTP POST data
     *
     * This factory method returns a new instance of this class by
     * populating the properties of the instance with data submitted in
     * the HTTP POST request. This method populates all properties of
     * this class except $this->id and $this->date since they are
     * generated only while writing a post to a file.
     *
     * @return Post New post constructed from HTTP POST data
     */
    public static function newPostFromInput()
    {
        $post = new Post();

        $post->title = Pal::get($_POST, 'title');
        $post->name = Pal::get($_POST, 'name');
        $post->parent = Pal::get($_POST, 'id');

        if (strtolower(Pal::get($_POST, 'secrecy')) === 'yes')
            $post->key = bin2hex(openssl_random_pseudo_bytes(20));

        $post->enabled = true;
        $post->code = Pal::get($_POST, 'code');

        $post->normalize();
        return $post;
    }


    /**
     * Constructs a new instance of this class from the specified file
     *
     * This factory method returns a new instance of this class by
     * populating the properties of the instance with data read from a
     * file specified by its path.
     *
     * @return Post New post constructed from data in file
     */
    public static function newPostFromFile($id, $path)
    {
        $data = file_get_contents($path);
        if (file_exists($path) === false)
            throw new RuntimeException("File not found $path\n",
                                       self::NOT_FOUND);


        if ($data === false)
            throw new RuntimeException("Could not read $path\n",
                                       self::READ_ERROR);


        // Separate header from code
        $headerEndPos =  strpos($data, "\n\n");
        $header = substr($data, 0, $headerEndPos);
        $header = explode("\n", $header);

        // Create new post and populate code
        $post = new Post();
        $post->id = $id;
        $post->code = substr($data, $headerEndPos + 2);

        // Populate other properties of code from header
        foreach ($header as $line) {

            $tokens = explode(':', $line, 2);
            $key = strtolower($tokens[0]);
            $value = ltrim($tokens[1]);

            if ($key === 'date')
                $post->date = $value;
            else if ($key === 'title')
                $post->title = $value;
            else if ($key === 'name')
                $post->name = $value;
            else if ($key === 'parent')
                $post->parent = $value;
            else if ($key === 'key')
                $post->key = $value;
            else if ($key === 'enabled')
                $post->enabled = strtolower($value) === "yes" ? true
                                                              : false;
        }

        return $post;
    }


    /**
     * Validates the content of this post
     * 
     * This method ensures that the required properties of this post are
     * set and contain sensible values. This method returns an empty
     * array if no errors are found; otherwise it returns an array of
     * strings where each string describes an error.  
     *
     * @return array Array of error strings
     */
    public function validate()
    {
        $errors = array();

        // Code must be present
        if ($this->code === '') {
            $errors[] = 'Code input box must not be empty.';
        }

        // Title should not contain any tags
        if (preg_match('/<\w+>/', $this->title) === 1) {

            $errors[] = 'Title must not contain HTML tags.';
        }

        // Author's name should not contain special characters
        if (preg_match('/^[a-zA-Z\.\' ]*$/', $this->name) === 0) {

            $errors[] = 'Name must consist of letters ' .
                        'from the English alphabet, ' .
                        'single-quotes, dots, and spaces only.';
        }

        return $errors;
    }


    /**
     * Writes the content of this post to a file
     *
     * @param string $path Path of the file where content of this post
     *                     is to be written
     *
     * @return void
     * @throws RuntimeException If this method fails to write to the
     *                          specified file
     */
    public function write($path)
    {
        $file = fopen($path, 'w');
        if ($file === false)
            throw new RuntimeException(
                    "Could not open $path for writing",
                    self::WRITE_ERROR);

        $s = 'Date: ' . gmdate('Y-m-d H:i:s +0000') . "\n" .
             'Title: ' . $this->title . "\n" .
             'Name: ' . $this->name . "\n" .
             'Parent: ' . $this->parent . "\n" .
             'Key: ' . $this->key . "\n" .
             'Enabled: ' . 'Yes' . "\n\n" .
             $this->code;

        $success = fwrite($file, $s);
        if ($success === false)
            throw new RuntimeException("Could not write to $path",
                                       self::WRITE_ERROR);

        $success = fclose($file);
        if ($success === false)
            throw new RuntimeException("Could not close $path",
                                       self::WRITE_ERROR);
    }


    /**
     * Writes preview code of this post to a file
     *
     * The preview code is a code that can be processed by pandoc. The
     * code would be saved in a markdown file. This code would be read
     * by pandoc to convert it into a PDF.
     *
     * @param string $path Path of the file where preview code of this
     *                     post is to be written
     *
     * @return void
     *
     * @throws RuntimeException If this method fails to write to the
     *                          specified file
     */
    public function writePreview($path)
    {
        $file = fopen($path, 'w');
        if ($file === false)
            throw new RuntimeException(
                    "Could not open $path for writing",
                    self::WRITE_ERROR);


        $success = fwrite($file, $this->getPreviewCode());
        if ($success === false)
            throw new RuntimeException("Could not write to $path",
                                       self::WRITE_ERROR);

        $success = fclose($file);
        if ($success === false)
            throw new RuntimeException("Could not close $path",
                                       self::WRITE_ERROR);
    }


    /**
     * Returns the preview code for this post
     *
     * The preview code is a code that can be processed by pandoc. The
     * code would be saved in a markdown file. This code would be read
     * by pandoc to convert it into a PDF.
     *
     * @return string Preview code
     */
    public function getPreviewCode()
    {
        return '% ' . $this->title . "\n" .
               '% ' . $this->name . "\n" .
               $this->code;
    }


    /**
     * Returns a hash of the preview code for this post
     *
     * The preview code of a post is saved in the cache directory. The
     * preview hash is used as the key to read preview code saved in the
     * cache directory. This hash is used in the filenames of the files
     * saved in the cache directory.
     *
     * @return string Preview hash
     */
    public function getPreviewHash()
    {
        return sha1($this->getPreviewCode());
    }


    /**
     * Normalizes the content of this post
     *    
     * This method normalizes the code submitted in a post to use Unix
     * style line endings and to end with at least one trailing newline
     *
     * @return void
     */
    private function normalize()
    {
        // Normalize headers
        $this->title = trim($this->title);
        $this->name = trim($this->name);

        // Normalize line endings to Unix style line endings
        $this->code = str_replace("\r\n", "\n", $this->code);

        // Always terminate the file with a line ending
        $len = strlen($this->code);
        if ($this->code[$len - 1] !== "\n") {
            $this->code .= "\n";
        }
    }
}
?>
