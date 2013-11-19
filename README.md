MathB
=====
MathB is a web application that runs [MathB.in][1]. It is a pastebin for
mathematics. One can share snippets of mathematical text using this
application. The snippets can be written in LaTeX, Markdown as well as
HTML.

  [1]: http://mathb.in/


Source code setup
-----------------
MathB depends on third-party JavaScript libraries to convert users's
input specified in LaTeX and Markdown into HTML that can be rendered in
the output. These third-party JavaScript libraries are:

  1. [Pagedown][T1]
  2. [MathJax][T2]

These libraries are not included in the source code. They should be
downloaded separtely and placed in the following two directories
relative to this project's top level directory, i.e. the directory that
contains the favicon.ico file.

  1. thirdparty/pagedown
  2. thirdparty/MathJax

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

  [T1]: http://code.google.com/p/pagedown/
  [T2]: http://www.mathjax.org/


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
