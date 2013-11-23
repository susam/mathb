<?php
/**
 * MathB bag of strings
 *
 * This script contains the View class that contains the definition of
 * the view to be displayed to the user.
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

use DOMDocument;
use Susam\Pal;
use Michelf\Markdown;


/**
 * Contains a collection of strings to be displayed in the pages
 *
 * An instance of this class is a container of strings that are useful
 * to display various texts in the pages of this application.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://mathb.in/5 Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class Bag
{

    /**
     * ID of the current post
     *
     * @var string
     */
    public $postID;


    /**
     * Title of the current post to be displayed in the input form
     *
     * @var string
     */
    public $inputTitle;


    /**
     * Author of the current post to be displayed in the input form
     *
     * @var string
     */
    public $inputName;


    /**
     * Code of the current post to be displayed in the input form
     *
     * @var string
     */
    public $inputCode;


    /**
     * Title of the current post to be displayed in the output sheet
     *
     * @var string
     */
    public $outputTitle;


    /**
     * Author of the current post to be displayed in the output sheet
     *
     * @var string
     */
    public $outputName;


    /**
     * Code of the current post to be displayed in the output sheet
     *
     * @var string
     */
    public $outputCode;


    /**
     * Class of title on the output sheet
     *
     * The value of this variable is 'none' if there is no title,
     * 'block' otherwise.
     *
     * @var string
     */
    public $outputTitleClass;


    /**
     * Class of author's name on the output sheet
     *
     * The value of this variable is 'none' if there is no author's
     * name, 'block' otherwise.
     *
     * @var string
     */
    public $outputNameClass;


    /**
     * Creation time of the current post
     *
     * @var string
     */
    public $date;


    /**
     * URL of the preview image
     *
     * @var string
     */
    public $previewImageURL;


    /**
     * URL to the current post
     *
     * @var string
     */
    public $postURL;


    /**
     * Input form is submitted to this URL with HTTP POST method
     *
     * @var string
     */
    public $actionURL;


    /**
     * The value of this property is 'checked' if the post contains a
     * secret key; otherwise it is '', i.e. an empty string
     *
     * @var string
     */
    public $secrecyAttribute;


    /**
     * HTML title of the page
     *
     * @var string
     */
    public $pageTitle;


    /**
     * Version of this application
     *
     * @var string
     */
    public $applicationVersion;


    /**
     * Whitelist of HTML tags and attributes allowed in a post
     *
     * @var array
     */
    private static $htmlWhitelist;


    /**
     * Constructs a new instance of this class
     *
     * This constructor accepts a Post object as an argument. It reads
     * the content contained in this object and sets the various string
     * properties of the constructed instance of this class. It uses the
     * Configuration object to determine post URL from the post ID.
     *
     * @param Configuration $conf Application configuration
     * @param Post          $post Post
     */
    public function __construct($conf = null, $post = null)
    {
        $this->applicationVersion = MathB::VERSION;

        if (isset($conf) && isset($post))
            $this->init($conf, $post);
        else
            $this->reset();
    }


    /**
     * Sets the string properties of this object to default values
     *
     * This method assigns default values to each property of this
     * object. This method may be called whenever this object should be
     * reset to its default state, i.e. all properties set to default
     * values.
     *
     * @return void
     */
    private function reset()
    {
        $this->postID = '';
        $this->inputTitle = '';
        $this->inputName = '';
        $this->inputCode = '';
        $this->outputTitle = '';
        $this->outputName = '';
        $this->outputCode = '';
        $this->outputTitleClass = '';
        $this->outputNameClass = '';
        $this->date = '';
        $this->previewImageURL = '';
        $this->postURL = '';
        $this->actionURL = '';
        $this->secrecyAttribute = '';
        $this->pageTitle = '';
    }


    /**
     * Sets the string properties of this object
     *
     * This method accepts a Post object as an argument. It reads the
     * content contained in this object and sets the various string
     * properties of the constructed instance of this class. It uses the
     * Configuration object to determine post URL from the post ID.
     *
     * @param Configuration $conf Application configuration
     * @param Post          $post Post
     *
     * @return void
     */
    private function init($conf, $post)
    {
        // Set post data
        $this->postID = $post->id;
        if ($post->id !== '') {
            $this->date = gmstrftime('%A, %e %B %Y %H:%M GMT',
                                     strtotime($post->date));
        }

        // Convert HTML whitelist JSON to PHP array
        self::$htmlWhitelist = json_decode(self::getHTMLWhitelistJSON(),
                                           true);

        // Set input form data
        $this->inputTitle = htmlspecialchars($post->title);
        $this->inputName = htmlspecialchars($post->name);
        $this->inputCode = htmlspecialchars($post->code);

        // Parse Markdown in code and sanitize HTML. It is necessary to
        // parse Markdown before sanitizing HTML because Markdown code
        // may contain URLs or header files in C code block within angle
        // brackets (e.g. <http://mathb.in/>, <stdio.h>, etc.) which
        // look like tags, and they will be removed by the sanitizer if
        // we sanitize the HTML first. We want Markdown parser to
        // process them first and convert them to proper HTML entities
        // or tags before the sanitizer kicks in.
        $code = Markdown::defaultTransform($post->code);

        // Set output sheet data
        $this->outputTitle = self::sanitize($post->title);
        $this->outputName = self::sanitize($post->name);
        $this->outputCode = self::sanitize($code);

        if ($this->outputTitle === '')
            $this->outputTitleClass = 'class="hidden"';

        if ($this->outputName === '')
            $this->outputNameClass = 'class="hidden"';

        $preview = new Preview($conf->getCacheDirectoryPath());
        $this->previewImageURL = $preview->getPNGURL($post);

        $this->postURL = $post->id !== '' ?
                         $conf->getPostURL($post->id, $post->key) : '';
        $this->actionURL = Pal::getHostURL() . '?post';

        // Set HTML attribute
        if ($post->key !== '')
            $this->secrecyAttribute = 'checked';

        // Set HTML title
        $title = strip_tags($post->title);
        $regex = '/\\\\[$\\[\\]\\(\\)]?/';
        $this->pageTitle = trim(preg_replace($regex, '', $title));
    }


    /**
     * Sanitizes HTML code
     *
     * This method removes all tags and attributes that are not in
     * self::$htmlWhiteList.
     *
     * @param string $html HTML code
     *
     * @return string Sanitized HTML code
     */
    private static function sanitize($html)
    {
        $dom = self::getDOM($html);
        self::sanitizeDOM($dom);
        return self::getHTML($dom);
    }


    /**
     * Sanitizes the attributes in an HTML node
     *
     * This method checks all the attributes in an HTML node and
     * removes those which do not occur in self::$htmlWhiteList.
     * Additionally, it removes 'href' attribute if it does not contain
     * a HTTP, FTP or mailto URL.
     *
     * @param DOMElement $node HTML DOM node to be sanitized
     *
     * @return void
     */
    private static function sanitizeTag($node)
    {
        $nodeName = strtolower($node->nodeName);
        for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
            $attrName = $node->attributes->item($i)->nodeName;

            if (! array_search(strtolower($attrName),
                               self::$htmlWhitelist[$nodeName])) {
                $node->removeAttribute($attrName);
                continue;
            }

            if (strtolower($attrName) === 'href') {
                $hrefValue = strtolower($node->attributes->item($i)->nodeValue);
                if (preg_match('/^https?:|ftp:|mailto:|\//', $hrefValue) === 0) {
                    $node->removeAttribute(attrName);
                }
            }
        }
    }


    /**
     * Sanitizes the tags in an HTML node
     *
     * This method walks through the DOM tree of the specified HTML
     * DOM node and removes all elements that do not occur in
     * my.htmlWhiteList.
     *
     * @param DOMElement $node HTML DOM node to be sanitized
     *
     * @return void
     */
    private static function sanitizeDOM($node)
    {
        if (! isset($node->childNodes))
            return;

        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {

            $childNode = $node->childNodes->item($i);
            $childNodeName = strtolower($childNode->nodeName);

            // Remove anything that isn't text or an HTML element.
            if ($childNode->nodeType != XML_ELEMENT_NODE &&
                $childNode->nodeType != XML_TEXT_NODE) {
                $node->removeChild($childNode);
                continue;
            }

            // Remove tags that can mess with the user interface.
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                if (! array_key_exists($childNodeName,
                                       self::$htmlWhitelist)) {
                    $node->removeChild($childNode);
                    continue;
                }
                
                self::sanitizeTag($childNode);
            }

            // Remove attributes that can mess with the user interface.
            self::sanitizeDOM($childNode);
        }
    }


    /**
     * Converts HTML code into DOM element
     *
     * The HTML code is wrapped around an HTML div element with a unique
     * ID and then it is converted into HTML DOM element.
     *
     * @param string $html HTML code
     *
     * @return DOMElement HTML DOM element
     */
    private static function getDOM($html)
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $success = $doc->loadHTML('<div id="_mathb_code">' .
                                  $html .
                                  '</div>');
        return $doc->getElementById("_mathb_code");
    }


    /**
     * Converts DOM element into HTML code
     *
     * The DOM element should be an element returned by self::getDOM
     * method. The DOM element is converted to HTML code, then the
     * wrapper div element is removed from the code and the resulting
     * code is returned.
     *
     * @param DOMElement $node HTML DOM element
     *
     * @return string HTML code
     */
    private static function getHTML($node)
    {
        $html = $node->ownerDocument->saveHTML($node);
        $prefixLen = strlen('<div id="_mathb_code">');
        $suffixLen = strlen('</div>');
        $htmlLen = strlen($html) - $prefixLen - $suffixLen;
        return substr($html, $prefixLen, $htmlLen);
    }


    /**
     * Returns the HTML tag whitelist as JSON string
     *
     * The JSON string contains a whitelist of HTML tags and attributes
     * allowed in a post
     *
     * @return string
     */
    private static function getHTMLWhitelistJSON()
    {
        return <<<JAVASCRIPT
        {
            "a":          ["class", "id", "style", "name", "href"],
            "abbr":       ["class", "id", "style"],
            "address":    ["class", "id", "style"],
            "article":    ["class", "id", "style"],
            "aside":      ["class", "id", "style"],
            "b":          ["class", "id", "style"],
            "blockquote": ["class", "id", "style"],
            "caption":    ["class", "id", "style"],
            "br":         ["class", "id", "style"],
            "cite":       ["class", "id", "style"],
            "code":       ["class", "id", "style"],
            "dd":         ["class", "id", "style"],
            "del":        ["class", "id", "style"],
            "details":    ["class", "id", "style"],
            "dfn":        ["class", "id", "style"],
            "div":        ["class", "id", "style"],
            "dl":         ["class", "id", "style"],
            "dt":         ["class", "id", "style"],
            "em":         ["class", "id", "style"],
            "footer":     ["class", "id", "style"],
            "h1":         ["class", "id", "style"],
            "h2":         ["class", "id", "style"],
            "h3":         ["class", "id", "style"],
            "h4":         ["class", "id", "style"],
            "h5":         ["class", "id", "style"],
            "h6":         ["class", "id", "style"],
            "header":     ["class", "id", "style"],
            "hgroup":     ["class", "id", "style"],
            "hr":         ["class", "id", "style"],
            "i":          ["class", "id", "style"],
            "img":        ["class", "id", "style", "src", "alt"],
            "kbd":        ["class", "id", "style"],
            "li":         ["class", "id", "style"],
            "ol":         ["class", "id", "style"],
            "p":          ["class", "id", "style"],
            "pre":        ["class", "id", "style"],
            "progress":   ["class", "id", "style"],
            "q":          ["class", "id", "style"],
            "s":          ["class", "id", "style"],
            "samp":       ["class", "id", "style"],
            "section":    ["class", "id", "style"],
            "small":      ["class", "id", "style"],
            "span":       ["class", "id", "tyle"],
            "strong":     ["class", "id", "style"],
            "sub":        ["class", "id", "style"],
            "summary":    ["class", "id", "style"],
            "sup":        ["class", "id", "style"],
            "table":      ["class", "id", "style"],
            "tbody":      ["class", "id", "style"],
            "td":         ["class", "id", "style"],
            "tfoot":      ["class", "id", "style"],
            "th":         ["class", "id", "style"],
            "thead":      ["class", "id", "style"],
            "time":       ["class", "id", "style"],
            "tr":         ["class", "id", "style"],
            "ul":         ["class", "id", "style"],
            "var":        ["class", "id", "style"],
            "wbr":        ["class", "id", "style"]
        }
JAVASCRIPT;
    }
}
?>
