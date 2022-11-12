all: favicon dist clear

# With border=0px in favicon.tex (tight cropping), the width of the
# standalone image is 11 (density=72) or 190 (density=1300). With
# further 17.4% padding on each side, we get a total image width of
# 190 * 1.348 = 256.
ImageOptim = /Applications/ImageOptim.app/Contents/MacOS/ImageOptim
favicon:
	TEXMFHOME=texmf pdflatex favicon.tex
	convert -density 1300 favicon.pdf favicon.tmp1.png
	convert -density 1300 favicon.pdf -gravity center -crop 256x256+0+0 favicon.tmp2.png
	cp favicon.tmp2.png favicon.png
	if command -v "$(ImageOptim)"; then "$(ImageOptim)" favicon.png; fi
	convert favicon.tmp2.png favicon.ico
	file favicon*.png favicon.ico
	ls -l favicon*png favicon.ico

dist:
	cp favicon.png favicon.ico ../../web/img/

deps:
	if command -v brew; then brew install --cask imageoptim; fi
	rm -rf texmf texmfvar ~/Library/texlive
	TEXMFHOME=texmf tlmgr init-usertree
	TEXMFHOME=texmf tlmgr --usermode install standalone

clear: clean
	rm -f *.pdf *.png *.ico

clean:
	rm -f *.aux *.log
