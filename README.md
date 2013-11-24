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

System setup
------------
This application depends on a few Linux tools to display static preview
for users who have JavaScript disabled in their browser. The static
preview is provided by converting the input code to a PNG with the help
of pandoc and convert commands, and displaying this PNG to the browser.
The following packages need to be installed:

  1. pandoc
  2. texlive,
  3. texlive-latex-extra
  4. imagemagick.

On Debian, or a Debian based Linux system, these packages can be
installed by running the following command:

    aptitude install pandoc texlive texlive-latex-extra imagemagick


Source code setup
-----------------
MathB depends on third-party JavaScript libraries to convert users's
input specified in LaTeX and Markdown into HTML that can be rendered in
the output. These third-party JavaScript libraries are:

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
http://mathb.in/5 to obtain a copy of the license.
