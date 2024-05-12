#!/bin/sh
socat TCP-LISTEN:4343,fork,reuseaddr TCP:127.0.0.1:4242 &
sbcl --load ~/quicklisp/setup.lisp --load mathb.lisp

