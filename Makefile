NAME = mathb
FQDN = $(NAME).in
MAIL = $(FQDN)@yahoo.com

help:
	@echo 'Usage: make [target]'
	@echo
	@echo 'High-level targets:'
	@echo '  setup       Install Debian packages.'
	@echo '  https       Reinstall live website and serve with Nginx via HTTPS.'
	@echo '  http        Reinstall live website and serve with Nginx via HTTP.'
	@echo '  rm          Uninstall live website.'
	@echo
	@echo 'Low-level targets:'
	@echo '  live        Generate live website.'
	@echo '  site        Generate local website.'
	@echo '  pull        Pull latest Git commits but do not update live website.'
	@echo
	@echo 'Test targets:'
	@echo '  test        Test Common Lisp program.'
	@echo
	@echo 'Default target:'
	@echo '  help        Show this help message.'

setup:
	apt-get update
	apt-get -y install nginx certbot sbcl
	rm -rf /opt/quicklisp.lisp /opt/quicklisp
	curl https://beta.quicklisp.org/quicklisp.lisp -o /opt/quicklisp.lisp
	sbcl --load /opt/quicklisp.lisp \
	     --eval '(quicklisp-quickstart:install :path "/opt/quicklisp/")' \
	     --quit
	chown -R www-data:www-data /opt/quicklisp

https: http
	@echo Setting up HTTPS website ...
	certbot certonly -n --agree-tos -m '$(MAIL)' --webroot \
	                 -w '/var/www/$(FQDN)' -d '$(FQDN),www.$(FQDN),susam.in'
	(crontab -l | sed '/::::/d'; cat etc/crontab) | crontab
	ln -snf "$$PWD/etc/nginx/https.$(FQDN)" '/etc/nginx/sites-enabled/$(FQDN)'
	systemctl reload nginx
	@echo Done; echo

http: rm live mathb
	@echo Setting up HTTP website ...
	ln -snf "$$PWD/_live" '/var/www/$(FQDN)'
	ln -snf "$$PWD/etc/nginx/http.$(FQDN)" '/etc/nginx/sites-enabled/$(FQDN)'
	systemctl reload nginx
	echo 127.0.0.1 '$(NAME)' >> /etc/hosts
	@echo Done; echo

mathb:
	@echo Setting up mathb ...
	mkdir -p /opt/data/mathb
	chown -R www-data:www-data /opt/data/mathb
	systemctl enable "/opt/mathb.in/etc/mathb.service"
	systemctl daemon-reload
	systemctl start mathb
	@echo Done; echo

rm: checkroot
	@echo Removing website ...
	rm -f '/etc/nginx/sites-enabled/$(FQDN)'
	rm -f '/var/www/$(FQDN)'
	systemctl reload nginx
	sed -i '/$(NAME)/d' /etc/hosts
	@echo
	@echo Removing mathb ...
	-systemctl stop mathb
	-systemctl disable mathb
	systemctl daemon-reload
	@echo
	@echo Following crontab entries left intact:
	crontab -l | grep -v "^#" || :
	@echo Done; echo

live: site
	@echo Setting up live directory ...
	mv _live _gone || :
	mv _site _live
	rm -rf _gone
	@echo Done; echo

site:
	@echo Setting up site directory ...
	rm -rf _site/
	cp -R static/ _site/
	git -C _site/js/ clone -b 1.2.0 --depth 1 https://github.com/susam/texme.git
	git -C _site/js/ clone -b v4.1.0 --depth 1 https://github.com/markedjs/marked.git
	git -C _site/js/ clone -b 3.2.2 --depth 1 https://github.com/mathjax/mathjax.git
	rm -rf _site/js/texme/.git
	rm -rf _site/js/marked/.git/
	rm -rf _site/js/mathjax/.git/
	@echo Done; echo

run:
	sbcl --load mathb.lisp

test:
	sbcl --noinform --eval "(defvar *quit* t)" --load test.lisp

checkroot:
	@echo Checking if current user is root ...
	[ $$(id -u) = 0 ]
	@echo Done; echo
