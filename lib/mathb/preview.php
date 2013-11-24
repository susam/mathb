<?php
/**
 * MathB preview files generation
 *
 * This script contains a class that offers methods to create preview
 * files.
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
 *   1. Redistributions of source  must retain the above copyright
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
 * Creates preview files
 *
 * This class contains method that can be used to generate preview of
 * the post at the server-side.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://mathb.in/5 Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class Preview
{
    /**
     * Path to the cache directory
     *
     * Output files and intermediate files to be cached are created in
     * this directory.
     *
     * @var string
     */
    private $cacheDirectoryPath;


    /**
     * Constructs an instance of this class
     *
     * @param string $cacheDirectoryPath Path to the cache directory
     */
    public function __construct($cacheDirectoryPath)
    {
        $this->cacheDirectoryPath = $cacheDirectoryPath;
    }


    /**
     * Cache the specified  in the cache directory
     *
     * This method caches the specified  in a file with filename
     * derived from the SHA1 hash of the , and saves the file in the
     * cache directory. If the  already exists in the cache, this
     * method does not write the  to the cache again.
     *
     * @param string $post Post to be cached
     *
     * @throws RuntimeException If could cannot be cached
     */
    public function cachePost($post)
    {
        $hash = $post->getPreviewHash();

        $lock = $this->lock($hash);

        $mdPath = $this->getMDPath($hash);
        if (is_file($mdPath) === false)
            $post->writePreview($mdPath);

        $this->unlock($lock);
        $this->rmlock($hash);
    }


    /**
     * Reads the specified file from cache and sends it to the client
     *
     * This method sends the specified file from the cache to the
     * client. If the file cannot be recognized, then it sends an HTTP
     * 404 response to the client. If the file is absent and it could
     * not be generated, then this method throws an exception which
     * would automatically cause an HTTP 500 response to be sent to the
     * client.
     *
     * @param string $filename Name of the file requested
     *
     * @throws RuntimeException If the requested file count not be
     *                          generated
     */
    public function sendFile($filename)
    {
        $tokens = explode('.', $filename);
        if (count($tokens) < 2) {
            http_response_(404);
            return;
        }

        $hash = $tokens[0];
        $type = $tokens[1];

        if ($type === 'png')
            $this->sendPNG($hash);
        else
            http_response_(404);
    }


    /**
     * Reads PNG file with the specified hash and sends it to the client
     *
     * This method first checks if the PNG file with the given hash
     * exists. If it does not exist, it attempts to create it from the
     * corresponding  that is cached. If the corresponding code does
     * not exist in the cache, an HTTP 404 response is sent. If an error
     * occurs while creating the PNG file, an exception is thrown.
     *
     * @param string $hash Preview hash of the PNG file
     */
    public function sendPNG($hash)
    {
        $lock = $this->lock($hash);
        $mdPath = $this->getMDPath($hash);
        if (is_file($mdPath) === false) {
            http_response_code(404);
            return;
        }

        $pngPath = $this->getPNGPath($hash);
        if (is_file($pngPath) === false) {
            $this->createPNG($hash);
        }

        $content = file_get_contents($pngPath);
        $length = filesize($pngPath);
        if ($content === false)
            throw new RuntimeException("Could not read $pngPath\n");
        $this->unlock($lock);
        $this->rmlock($hash);
        echo $content;
    }


    /**
     * Returns URL of the preview image with the specified hash
     *
     * @param string $hash Hash of the preview file
     *
     * @return URL of the preview image
     */
    public function getPNGURL($post)
    {
        return Pal::getHostURL() . 'preview/' .
               $post->getPreviewHash() . '.png';
    }


    /**
     * Creates a PNG image from the specified Markdown 
     *
     * @param string $hash Hash of the preview file
     *
     * @return void
     *
     * @throws RuntimeException If an error occurs while generating PNG
     */
    private function createPNG($hash)
    {
        $mdPath = $this->getMDPath($hash);
        $pngPath = $this->getPNGPath($hash);
        $pdfPath = $mdPath . '.pdf';
        $templatePath = $_SERVER['DOCUMENT_ROOT'] . 'resource/png.tex';

        $command = 'pandoc -f markdown ' .
                   "--template=$templatePath $mdPath -o $pdfPath " .
                   '2>&1';
        exec($command, $output, $status);

        if ($status !== 0) {
            self::createErrorPNG($pngPath, implode('\n', $output));
            return;
        }

        $command = 'convert -density 110 ' .
                   "$pdfPath $pngPath 2>&1";
        exec($command, $output, $status);

        // The PDF file is no longer required, and the following
        // statements might result in uncaught exception, so this is a
        // good time to delete the PDF file
        if (is_file($pdfPath) === true)
            unlink($pdfPath);

        if ($status !== 0)
            self::createErrorPNG($pngPath, implode('\n', $output));
    }


    /**
     * Creates a PNG image that displays error
     *
     * This method creates a PNG image to display error that occurred
     * while generating output PNG image for static preview.
     *
     * @param string $pngPath Path to the PNG image
     * @param string $error   Error string
     *
     * @return void
     *
     * @throws RuntimeException If an error occurs while generating PNG
     */
    private static function createErrorPNG($pngPath, $error)
    {
        $error = preg_replace('/[\'"]/', '', $error);
        $error = str_ireplace($_SERVER['DOCUMENT_ROOT'], '_ROOT_',
                              $error);
        $error = "ERROR:\n\n" . wordwrap($error, 64, "\n", true);

        $command = 'convert -pointsize 16 -fill red -font helvetica ' .
                   '-size 600x300 ' .
                   "-draw 'text 2,16 \"$error\"' " .
                   "xc:transparent $pngPath 2>&1";

        exec($command, $output, $status);

        if ($status !== 0)
            throw new RuntimeException('Could not create error PNG: ' .
                                       implode(' - ', $output));
    }


    /**
     * Returns path to Markdown preview file for the specified hash
     *
     * @param string $hash Hash of the preview file
     *
     * @return string Path to Markdown preview file
     */
    private function getMDPath($hash)
    {
        return $this->getCacheFilePath($hash, 'md');
    }


    /**
     * Returns path to PNG preview file for the specified hash
     *
     * @param string $hash Hash of the preview file
     *
     * @return string Path to PNG preview file
     */
    private function getPNGPath($hash)
    {
        return $this->getCacheFilePath($hash, 'png');
    }


    /**
     * Returns path to preview lock file for the specified hash
     *
     * @param string $hash Hash of the preview file
     *
     * @return string Path to preview lock file
     */
     private function getLockPath($hash)
    {
        return $this->getCacheFilePath($hash, 'lock');
    }


    /**
     * Returns path to preview file with the specified hash and extension
     *
     * @param string $hash      Hash of the preview file in cache
     * @param string $extension Extension of the preview file in cache
     *
     * @return string Path to preview file
     */
    private function getCacheFilePath($hash, $extension)
    {
        return $this->cacheDirectoryPath . $hash . '.' . $extension;
    }


    /**
     * Acquire a lock on the preview files with the specified hash
     *
     * @param string $hash Hash of the preview files to be locked
     *
     * @return resource Lock file
     *
     * @throws RuntimeException If the lock cannot be acquired
     */
    private function lock($hash)
    {
        $lockFilePath = $this->getLockPath($hash);
        $lock = fopen($lockFilePath, 'w');
        if ($lock === false)
            throw new RuntimeException(
                    "Could not open $lockFilePath for writing");

        $success = flock($lock, LOCK_EX);
        if ($success === false)
            throw new RuntimeException("Could not lock $lockFilePath");

        return $lock;
    }


    /**
     * Release the specified lock
     *
     * @param resource $lock Lock file
     *
     * @return void
     *
     * @throws RuntimeException If the lock cannot be released
     */
    private function unlock($lock)
    {
        $success = flock($lock, LOCK_UN);
        if ($success === false)
            throw new RuntimeException("Could not unlock $lockFilePath");

        $success = fclose($lock);
        if ($success === false)
            throw new RuntimeException("Could not close $lockFilePath");
    }


    /**
     * Removes the lock file for the specified hash
     *
     * @param string $hash Hash of the preview files that were locked
     *
     * @return void
     */
    private function rmlock($hash)
    {
        $path = $this->getLockPath($hash);
        if (is_file($path) === true)
            unlink($path);
    }

}
