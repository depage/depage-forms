DOCREPO = git://github.com/depage/depage-docu.git
RM = rm -rf
BACKCOLOR = backcolor_brightblue

doc: depage-docu examples/main-*.html
	cd ./depage-docu/ ; git pull origin master || true
	doxygen Doxyfile
	cp -r depage-docu/www/lib html/
	sed -i".bak" "s/<body/<body class=\\\"home\\\"/" html/index.html
	sed -i".bak" "s/div id=\\\"backcolor_\\\"/div id=\\\"${BACKCOLOR}\\\"/" html/*.html
	rm html/*.bak

examples/main-*.html: ../README.md
	csplit ../README.md '/Prerequisites/'
	./depage-docu/www/md2html.php xx00 examples/main-intro.html
	./depage-docu/www/md2html.php xx01 examples/main-extended.html
	${RM} xx00 xx01

depage-docu:
	git clone -q ${DOCREPO}

clean:
	${RM} examples/main*.html
	${RM} depage-docu
	${RM} html
