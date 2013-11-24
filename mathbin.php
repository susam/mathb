<?php
/**
 * MathB.in main script
 *
 * This script instantiates the application and runs it.
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


require __DIR__ . '/thirdparty/php-markdown/Michelf/Markdown.php';

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/lib');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/lib/mathb');
spl_autoload_register();

use Susam\Session;
Session::beginSession($_SERVER['DOCUMENT_ROOT'] . '/../usage/mathbin-',
                      'Asia/Kolkata');

use MathB\MathB;
use MathB\Configuration;

$view = new MathBinView();
$conf = new Configuration();

// IP addresses of spammers
$conf->ipBlacklist = array(
    '/^96\.47\.225\..*$/',
    '/^96\.47\.224\..*$/',
    '/^209\.234\.85\.98$/',
);

$mathb = new MathB($conf, $view);
$mathb->run();

Session::endSession();
?>
