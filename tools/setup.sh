#!/bin/sh
mkdir thirdparty
hg --cwd thirdparty clone https://code.google.com/p/pagedown/
git clone git://github.com/mathjax/MathJax.git thirdparty/MathJax
