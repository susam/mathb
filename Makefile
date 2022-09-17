run:
	sbcl --load mathb.lisp

test:
	sbcl --noinform --eval "(defvar *quit* t)" --load test.lisp
