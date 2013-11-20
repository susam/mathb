#!/bin/sh
mkdir thirdparty
hg --cwd thirdparty clone https://code.google.com/p/pagedown/
git clone git://github.com/mathjax/MathJax.git thirdparty/MathJax
git clone https://github.com/michelf/php-markdown.git thirdparty/php-markdown
