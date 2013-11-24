<?php
/**
 * MathB.in view
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

use MathB\View;

/**
 * Defines view for MathB.in
 *
 * This class extends MathB\View in order to customize the look and feel
 * of the MathB.in website.
 *
 * @author Susam Pal <susam@susam.in>
 * @copyright 2012-2013 Susam Pal
 * @license http://mathb.in/5 Simplified BSD License
 * @version version 0.1
 * @since version 0.1
 */
class MathBinView extends View 
{
    /**
     * Outputs the link tags to load style sheets.
     *
     * In addition to calling the styles method of the parent class, it
     * defines new styles for new elements defined in this view such as
     * social media widgets, software stack credit, etc.
     *
     * @return void
     */
    protected function styles()
    {
        parent::styles();
?>
    <!-- MathBinView::styles -->
    <style type="text/css">
    #fb-widget {
        margin-bottom: 1em;
    }

    #footerPanel #widgets,
    #footerPanel #software,
    #footerPanel #copyright {
        vertical-align: top;
        text-align: left;
        display: inline-block;
        width: 30%;
        padding: 0 1em;
    }

    #footerPanel #widgets *:first-child,
    #footerPanel #software *:first-child,
    #footerPanel #copyright *:first-child {
        margin-top: 0;
    }
    </style>
<?php
    }


    /**
     * Outputs the header of the page
     *
     * @return void
     */
    protected function header()
    {
?><!-- MathBinView::header -->
    <div id="header">
        <h1>
            <a href="/"><?php $this->siteName() ?></a>
        </h1>
        <h2>Share mathematics on the web</h2>
    </div><div id="navigation">
        <span>[ <a href="/">new</a> ]</span>
        <span>[ <a href="/1">demo</a> ]</span>
        <span>[ <a href="/2">tutorial</a> ]</span>
        <span>[ <a href="/3">about</a> ]</span>
    </div>
<?php
    }


    /**
     * Outputs the footer of the page
     *
     * @return void
     */
    protected function footer()
    {
        $this->socialSDKs();
?>
    <!-- MathBinView::footer -->
    <div id="footer">
        <div id="widgets">
            <div id="fb-widget">
                <div class="fb-like-box"
                     data-href="http://www.facebook.com/mathbin"
                     data-height="188"
                     data-colorscheme="light" data-show-faces="true"
                     data-header="false" data-stream="false"
                     data-show-border="true"></div>
            </div>
            <div id="gplus-widget">
                <div class="g-page" data-width="300"
                     data-href="//plus.google.com/115560622639690814168"
                     data-layout="landscape" data-rel="publisher"></div>
            </div>
        </div> <!-- End widgets -->
        <div id="software">
            <p>
                <a href="/"><?php $this->siteName() ?></a> is powered by
                <a href="http://github.com/susam/mathb">MathB <?php echo
                $this->bag->applicationVersion ?></a>.
            </p>
            <p>
                LaTeX rendering is powered by <a
                href="http://www.mathjax.org">MathJax</a>.
            </p>
            <p>
                Markdown rendering is powered by <a
                href="http://code.google.com/p/pagedown/">PageDown</a>.
            </p>
            <a href="/"><img src="images/mathb.png"
                 title="MathB.in" alt="MathB.in"></a>
            <a href="http://www.mathjax.org"><img
            src="images/mathjax/badge-square-2.png" title="MathJax"
            alt="MathJax"></a>
        </div>
        <div id="copyright">
            <p>
            <a href="/"><?php $this->siteName() ?></a><br>
            &copy; 2013 <a href="http://susam.in">Susam Pal</a>
            </p><p>
            This is free software. You are permitted to redistribute and
            use it in source and binary forms, with or without modification,
            under the terms of the <a href="/5">Simplified BSD
            License</a>.
            </p>
        </div>
    </div>
<?php
    }


    /**
     * Outputs code to load social media SDKs
     *
     * This method outputs the HTML and JavaScript code to load the
     * JavaScript SDKs of social media widgets.
     *
     * @return void
     */
    private function socialSDKs()
    {
?><!-- MathBinView::socialSDKs -->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <script type="text/javascript"
            src="https://apis.google.com/js/plusone.js"></script>
<?php
    }


    /**
     * Outputs MathB.in as the site name
     *
     * @return void
     */
    protected function siteName()
    {
        echo "MathB.in";
    }


    /**
     * Outputs a link to a description of secret URLs
     *
     * @return void
     */
    protected function secrecyTips()
    {
?> <!-- MathBinView::secrecyTips -->
                        (<a target="_blank" href="/4">Learn more</a>)
<?php
    }
}
?>
