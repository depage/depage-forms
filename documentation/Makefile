DOCREPO = git@github.com:jonasjonas/depage-docu.git
RM = rm -rf

doc: depage-docu
	cd ./depage-docu/ ; git pull origin master
	doxygen Doxyfile
	cp -r depage-docu/www/lib html/
	sed -i "" "s/\<body/\<body class=\\\"home\\\"/" html/index.html

depage-docu:
	git clone -q ${DOCREPO}

clean:
	${RM} depage-docu
	${RM} html
