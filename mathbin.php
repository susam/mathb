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


require __DIR__ . '/thirdparty/php-markdown/Michelf/Markdown.inc.php';

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/lib');
spl_autoload_register();

use Susam\Session;
Session::beginSession($_SERVER['DOCUMENT_ROOT'] . '/../usage/mathbin-',
                      'Asia/Kolkata');

use MathB\MathB;
use MathB\Configuration;

$view = new MathBinView();
$conf = new Configuration();
$conf->enableStaticPreview();

// IP addresses of spammers
$conf->ipBlacklist = array(
    '/^32\.212\.81\..*$/',
    '/^36\.68\.88\..*$/',
    '/^36\.68\.100\..*$/',
    '/^36\.68\.103\..*$/',
    '/^36\.68\.124\..*$/',
    '/^36\.68\.130\..*$/',
    '/^36\.68\.132\..*$/',
    '/^36\.72\.95\..*$/',
    '/^36\.73\.237\..*$/',
    '/^36\.76\.69\..*$/',
    '/^36\.76\.95\..*$/',
    '/^36\.76\.163\..*$/',
    '/^36\.76\.169\..*$/',
    '/^36\.76\.190\..*$/',
    '/^36\.76\.203\..*$/',
    '/^36\.76\.210\..*$/',
    '/^36\.76\.216\..*$/',
    '/^36\.77\.60\..*$/',
    '/^36\.83\.106\..*$/',
    '/^36\.84\.52\..*$/',
    '/^37\.57\.84\..*$/',
    '/^39\.226\.62\..*$/',
    '/^46\.165\.220\..*$/',
    '/^50\.7\.78\..*$/',
    '/^54\.244\.57\..*$/',
    '/^59\.145\.168\..*$/',
    '/^62\.210\.78\..*$/',
    '/^62\.210\.167\..*$/',
    '/^66\.171\.229\..*$/',
    '/^66\.187\.75\..*$/',
    '/^69\.64\.52\..*$/',
    '/^70\.39\.185\..*$/',
    '/^76\.73\.41\..*$/',
    '/^77\.92\.72\..*$/',
    '/^77\.247\.181\..*$/',
    '/^78\.129\.148\..*$/',
    '/^78\.189\.164\..*$/',
    '/^82\.41\.82\..*$/',
    '/^82\.145\.208\..*$/',
    '/^82\.145\.210\..*$/',
    '/^82\.145\.216\..*$/',
    '/^83\.6\.137\..*$/',
    '/^83\.8\.140\..*$/',
    '/^84\.193\.237\..*$/',
    '/^84\.222\.14\..*$/',
    '/^85\.159\.237\..*$/',
    '/^87\.95\.170\..*$/',
    '/^89\.137\.77\..*$/',
    '/^93\.182\.131\..*$/',
    '/^93\.182\.133\..*$/',
    '/^95\.211\.174\..*$/',
    '/^96\.47\.224\..*$/',
    '/^96\.47\.225\..*$/',
    '/^103\.28\.149\..*$/',
    '/^103\.31\.235\..*$/',
    '/^103\.225\.188\..*$/',
    '/^103\.243\.53\..*$/',
    '/^110\.137\.130\..*$/',
    '/^110\.139\.90\..*$/',
    '/^110\.232\.93\..*$/',
    '/^112\.215\.66\..*$/',
    '/^114\.79\..*\..*$/',
    '/^114\.125\..*\..*$/',
    '/^117\.20\.57\..*$/',
    '/^118\.97\.95\..*$/',
    '/^120\.168\.0\..*$/',
    '/^120\.177\.181\..*$/',
    '/^120\.180\.54\..*$/',
    '/^122\.161\.5\..*$/',
    '/^122\.161\.12\..*$/',
    '/^122\.161\.52\..*$/',
    '/^122\.161\.57\..*$/',
    '/^122\.161\.85\..*$/',
    '/^122\.161\.85\..*$/',
    '/^122\.161\.169\..*$/',
    '/^122\.161\.185\..*$/',
    '/^125\.25\.94\..*$/',
    '/^125\.162\.125\..*$/',
    '/^139\.0\.123\..*$/',
    '/^139\.0\.144\..*$/',
    '/^139\.193\.129\..*$/',
    '/^174\.3\.186\..*$/',
    '/^178\.162\.193\..*$/',
    '/^180\.241\.34\..*$/',
    '/^180\.241\.93\..*$/',
    '/^180\.242\.6\..*$/',
    '/^180\.242\.26\..*$/',
    '/^180\.246\.74\..*$/',
    '/^180\.247\.77\..*$/',
    '/^180\.248\.163\..*$/',
    '/^180\.249\.2\..*$/',
    '/^180\.249\.110\..*$/',
    '/^180\.251\.1\..*$/',
    '/^180\.251\.72\..*$/',
    '/^180\.251\.240\..*$/',
    '/^180\.252\.82\..*$/',
    '/^180\.253\.150\..*$/',
    '/^180\.254\.153\..*$/',
    '/^180\.254\.166\..*$/',
    '/^180\.254\.181\..*$/',
    '/^182\.64\..*\..*$/',
    '/^182\.68\..*\..*$/',
    '/^182\.69\..*\..*$/',
    '/^188\.143\.232\..*$/',
    '/^195\.2\.240\..*$/',
    '/^198\.50\.103\..*$/',
    '/^202\.62\.16\..*$/',
    '/^202\.62\.17\..*$/',
    '/^202\.67\.44\..*$/',
    '/^202\.137\.4\..*$/',
    '/^202\.152\.202\..*$/',
    '/^203\.144\.92\..*$/',
    '/^203\.144\.93\..*$/',
    '/^209\.234\.85\.98$/',
    '/^212\.36\.207\..*$/',
    '/^213\.204\.106\..*$/',
    '/^213\.238\.175\..*$/',
);

/*
    The following IP addresses have been replaced with a more general
    regular expression.
    '/^114\.79\.1\..*$/',
    '/^114\.79\.29\..*$/',
    '/^114\.79\.28\..*$/',
    '/^114\.79\.47\..*$/',
    '/^114\.79\.61\..*$/',
    '/^114\.79\.62\..*$/',

    '/^114\.125\.46\..*$/',
    '/^114\.125\.47\..*$/',
    '/^114\.125\.49\..*$/',
    '/^114\.125\.49\..*$/',
    '/^114\.125\.61\..*$/',

    '/^182\.68\.49\..*$/',
    '/^182\.68\.52\..*$/',
    '/^182\.68\.102\..*$/',
    '/^182\.68\.152\..*$/',
    '/^182\.68\.154\..*$/',
    '/^182\.68\.163\..*$/',
    '/^182\.68\.185\..*$/',
*/

$mathb = new MathB($conf, $view);
$mathb->run();

Session::endSession();
?>
