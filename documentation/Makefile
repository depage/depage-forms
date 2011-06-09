DOCREPO = git@github.com:jonasjonas/depage-docu.git
RM = rm -rf

doc: depage-docu
	cd ./depage-docu/ ; git pull origin master
	./depage-docu/www/md2html.php ../README.md examples/main.html
	doxygen Doxyfile
	rm examples/main.html
	cp -r depage-docu/www/lib html/
	sed -i".bak" "s/\<body/\<body class=\\\"home\\\"/" html/index.html 
	rm html/*.bak

depage-docu:
	git clone -q ${DOCREPO}

clean:
	${RM} depage-docu
	${RM} html
