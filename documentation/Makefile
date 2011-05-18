DOCREPO = git@github.com:jonasjonas/depage-docu.git
RM = rm -rf

doc: depage-docu
	GIT_DIR=./depage-docu/.git git pull origin master
	doxygen Doxyfile
	cp -r depage-docu/www/lib html/

depage-docu:
	git clone -q ${DOCREPO}

clean:
	${RM} depage-docu
	${RM} html
