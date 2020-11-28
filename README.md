MathB
=====
MathB is a web application that runs [MathB.in][1]. It is a pastebin for
mathematics. One can share snippets of mathematical text using this
application. The snippets can be written in LaTeX, Markdown as well as
HTML.

  [1]: http://mathb.in/


Features
--------
The following features are supported by this application.

  1. Input can be provided as a free mix of LaTeX, Markdown and HTML
     code
  2. Math is rendered with HTML and CSS on browsers that have JavaScript
     enabled
  3. Math is rendered as PNG image on browsers that have JavaScript
     disabled
  4. Live preview if JavaScript is enabled
  5. Static preview if JavaScript is disabled
  6. Secret posts with secret URLs

To try out these features, visit <http://mathb.in>, a pastebin for math
powered by this project.


Source code setup
-----------------
MathB depends on third-party JavaScript and PHP ibraries to convert
users's input specified in LaTeX and Markdown into HTML that can be
rendered in the output. These third-party JavaScript libraries are:

  1. [PHP-Markdown][T1]
  2. [Pagedown][T2]
  3. [MathJax][T3]

These libraries are not included in the source code. They should be
downloaded separtely and placed in the following two directories
relative to this project's top level directory, i.e. the directory that
contains the favicon.ico file.

  1. thirdparty/php-markdown
  2. thirdparty/pagedown
  3. thirdparty/MathJax

There is shell script called setup.sh which can be executed to
automatically clone these third-party projects from their original
source code repositories to the directories specified in the list above.
To execute this script, first change your current working directory to
this project's top level directory, i.e. the current working directory
must be the directory that contains the main.php file. Then execute this
command:

    sh tools/setup.sh

This script requires the `git` and `hg` commands to be present. If they
are absent, install Git and Mercurial before running the script.

  [T1]: http://michelf.ca/projects/php-markdown/
  [T2]: http://code.google.com/p/pagedown/
  [T3]: http://www.mathjax.org/


Static preview setup
--------------------
This application normally requires JavaScript to be enabled in the
user's browser to display the output rendered from the input code. When
JavaScript is disabled in the user's browser, the application displays a
notice indicating that JavaScript needs to be enabled in the browser.

It is possible to configure the application to display the output using
PNG images when JavaScript is disabled. This is called a static preview.
It is static because the user cannot see a live preview of the output as
the input is typed. Instead, a preview button needs to be pressed
whenever the user wants to see a preview of the post. This is an
optional feature that is disabled by default and enabled if required.
When static preview is enabled, the notice indicating that JavaScript
needs to be enabled is no longer displayed when JavaScript is disabled
in the browser. Instead a static preview of the post rendered as a PNG
image is displayed in the output sheet.

Note that when static preview is enabled, it makes no difference to the
behaviour of the application when JavaScript is enabled in the user's
browser. Enabling this feature helps only those users who have
JavaScript disabled in their browser.

The static preview is provided by converting the input code to a PNG
with the help of `pandoc` and `convert` commands, and displaying this
PNG to the browser.  The following packages need to be installed:

  1. pandoc
  2. texlive,
  3. texlive-latex-extra
  4. imagemagick.

On Debian, or a Debian based Linux system, these packages can be
installed by running the following command:

    aptitude install pandoc texlive texlive-latex-extra imagemagick

Once these packages have been installed, the implementation script needs
to pass a configuration object to MathB class that has static preview
enabled. An example of how to do this can be found in mathbin.php. Here
is a minimal example of such a script.

```php
require __DIR__ . '/thirdparty/php-markdown/Michelf/Markdown.php';
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/lib');
spl_autoload_register();

use MathB\MathB;
use MathB\Configuration;

$conf = new Configuration();
$conf->enableStaticPreview();
$mathb = new MathB($conf);
$mathb->run();
```

To test if static preview is working fine, perform the following steps
in the application.


  1. Disable JavaScript in the browser.
  2. Visit the home page and type the following input:

        $$ e^{i \pi} + 1 $$

  3. Click the preview button.
  4. See if the output is rendered correctly using a PNG image in the
     output sheet.


License
-------
This is free software. You are permitted to redistribute and use it in
source and binary forms, with or without modification, under the terms
of the Simplified BSD License. See the LICENSE.md file for the complete
license.

This software is provided WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
LICENSE.md file for the complete disclaimer.

If you do not have a copy of the LICENSE.md file, please visit
<http://mathb.in/5> to obtain a copy of the license.
