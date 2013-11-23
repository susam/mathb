/*
SIMPLIFIED BSD LICENSE

Copyright (c) 2013 Susam Pal
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

  1. Redistributions of source code must retain the above copyright
     notice, this list of conditions and the following disclaimer.
  2. Redistributions in binary form must reproduce the above copyright
     notice, this list of conditions and the following disclaimer in
     the documentation and/or other materials provided with the
     distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

var MathB = function() {
    'use strict'

    // Object with properties shared by all MathB functions
    var my = {

        // Update details for code update
        codeUpdate: createCallback(updateCode, 250),

        // Update details for title update
        titleUpdate: createCallback(updateTitle, 125),

        // Update details for author update
        nameUpdate: createCallback(updateName, 125),

        // HTML elements used by the application
        html: {
            // Element that contains code in the input form
            code: null,

            // Element that contains the post title in the input form
            title: null,

            // Element that contains the author in the input form
            name: null,

            // Dynamic output sheet
            sheet: null,

            // Element that displays the code in the output sheet
            outputCode: null,

            // Element that displays the title in the output sheet
            outputTitle: null,

            // Element that displays the author in the output sheet
            outputName: null,

            // Element that contains permanent URL
            url: null
        },

        // Whitelist of HTML tags and attributes allowed in a post
        htmlWhiteList: {
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
            "span":       ["class", "id", "style"],
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
    }


    // Initialize MathB object
    function init()
    {
        // Get all necessary HTML elements
        var elementID
        for (elementID in my.html) {
            my.html[elementID] = document.getElementById(elementID)
        }

        my.html.sheet.style.display = 'block';

        defineArrayIndexOf()

        my.html.code.onkeyup = my.codeUpdate.schedule
        my.html.code.onchange = my.codeUpdate.schedule
        my.html.code.onpaste = my.codeUpdate.schedule
        my.html.code.oncut = my.codeUpdate.schedule

        my.html.title.onkeyup = my.titleUpdate.schedule
        my.html.title.onchange = my.titleUpdate.schedule
        my.html.title.onpaste = my.titleUpdate.schedule
        my.html.title.oncut = my.titleUpdate.schedule

        my.html.name.onkeyup = my.nameUpdate.schedule
        my.html.name.onchange = my.nameUpdate.schedule
        my.html.name.onpaste = my.nameUpdate.schedule
        my.html.name.oncut = my.nameUpdate.schedule

        // If permanent URL box exists, clicking it should select the
        // entire URL, so that the user can copy it with fewer
        // keystrokes
        if (my.html.url !== null)
            my.html.url.onclick = my.html.url.select

        my.codeUpdate.schedule()
        my.titleUpdate.schedule()
        my.nameUpdate.schedule()
    }


    // Create a callback that can be scheduled later
    //
    // Calling this function returns an object with a schedule method.
    // The schedule method of the returned object can be called to
    // schedule the specified callback to be called after the specified
    // delay.
    //
    // Arguments:
    //   callback -- Callback to be called when an update is scheduled
    //   delay    -- Delay after which callback should be called
    //
    // Return value:
    //   A callback scheduler object with a schedule method
    function createCallback(callback, delay)
    {
        var timeout = null

        // Schedule update of an output sheet element
        //
        // When the user edits an element in the input form, the
        // corresponding element of the output sheet is not updated
        // immediately for two reasons:
        //
        //   1. A fast typist can type 7 to 10 characters per second.
        //      Updating the output sheet so frequently, causes the user
        //      interface to become less responsive.
        //   2. The onpaste or oncut functions of an input element gets
        //      the old value of the element instead of the new value
        //      resulting from the cut or paste operation.
        //
        // This function works around the above issues by scheduling the
        // updateOutput function to be called after 250 milliseconds. This
        // ensures that the output sheet is not updated more than four times
        // per second. This also ensures that when the updateOutput
        // function is invoked as a result of a cut or paste operation on a
        // text field element, then it gets the updated value of the
        // element.
        function schedule()
        {
            if (timeout !== null) {
                window.clearTimeout(timeout)
                timeout = null
            }

            timeout = window.setTimeout(callback, delay)
        }

        // Callback scheduler methods
        return {
            schedule: schedule
        }
    }

    // Update the code in the output sheet
    //
    // This function converts the data specified in the input code into
    // HTML, and displays it on the output sheet.
    function updateCode()
    {
        // Replace \ with \\ so that Markdown converts \\ back to \
        var s = my.html.code.value
        s = s.replace(/\\/g, '\\\\')
        s = new Markdown.Converter().makeHtml(s)

        my.html.outputCode.innerHTML = s
        sanitizeDOM(my.html.outputCode)
        MathJax.Hub.Queue(['Typeset', MathJax.Hub, my.html.outputCode]);
    }


    // Update the title in the output sheet
    function updateTitle()
    {
        var s = my.html.title.value
        
        my.html.outputTitle.style.display = s === '' ? 'none' : 'block'
        my.html.outputTitle.innerHTML = s
        sanitizeDOM(my.html.outputTitle)
        MathJax.Hub.Queue(['Typeset', MathJax.Hub, my.html.outputTitle]);
    }


    // Update the author in the output sheet
    function updateName()
    {
        var s = my.html.name.value

        my.html.outputName.style.display = s === '' ? 'none' : 'block'
        my.html.outputName.innerHTML = s
        sanitizeDOM(my.html.outputName)
    }


    // Sanitize the attributes in an HTML node
    //
    // This function checks all the attributes in an HTML node and
    // removes those which do not occur in my.htmlWhiteList.
    // Additionally, it removes 'href' attribute if it does not contain
    // a HTTP, FTP or mailto URL.
    //
    // Arguments:
    //   node -- HTML DOM node
    function sanitizeTag(node)
    {
        var nodeName = node.nodeName.toLowerCase()
        for (var i = node.attributes.length - 1; i >= 0; i--) {
            var attrName = node.attributes[i].nodeName

            // Remove attribute if it is not in the whitelist
            if (my.htmlWhiteList[nodeName].indexOf(attrName.toLowerCase()) < 0) {
                node.removeAttribute(attrName)
                continue
            }

            // If the attribute name is 'href', make sure that that the
            // href value is not javascript 
            if (attrName.toLowerCase() == 'href') {
                var hrefValue = node.attributes[i].nodeValue.toLowerCase()
                if (!hrefValue.match(/^https?:|ftp:|mailto:|\//))
                    node.removeAttribute(attrName)
            }
        }
    }


    // Sanitize the tags in an HTML node
    //
    // This function walks through the DOM tree of the specified HTML
    // DOM node and removes all elements that do not occur in
    // my.htmlWhiteList.
    //
    // Arguments:
    //   node -- HTML DOM node
    function sanitizeDOM(node)
    {
        var ELEMENT_NODE = 1
        var TEXT_NODE = 3

        if (!node.childNodes)
            return

        for (var i = node.childNodes.length - 1; i >= 0; i--) {

            var childNode = node.childNodes[i]
            var childNodeName = childNode.nodeName.toLowerCase()

            // Remove anything that isn't text or an HTML element.
            if (childNode.nodeType != ELEMENT_NODE &&
                childNode.nodeType != TEXT_NODE) {
                node.removeChild(childNode)
                continue
            }

            // Remove tags that can mess with the user interface.
            if (childNode.nodeType == ELEMENT_NODE) {
                if (!(childNodeName in my.htmlWhiteList)) {
                    node.removeChild(childNode)
                    continue
                }
                
                sanitizeTag(childNode)
            }

            // Remove attributes that can mess with the user interface.
            sanitizeDOM(childNode)
        }
    }

    // Define an indexOf method for array if it does not exist
    function defineArrayIndexOf()
    {
        if (![].indexOf) {
            Array.prototype.indexOf = function(needle) {
                for (var i=0; i < this.length; i++)
                    if (this[i] == needle)
                        return i;
                return -1
            }
        }
    }

    // MathB methods
    return {
        init: init
    }
}()
