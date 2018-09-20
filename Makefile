RM = rm -rf

all: test doc

doc:
	cd Documentation ; git clone https://github.com/depage/depage-docu.git depage-docu || true
	doxygen Documentation/Doxyfile
	cp -r Documentation/depage-docu/www/lib Documentation/html/
	sed -i".bak" "s/<body class=\\\"\\\"/<body class=\\\"home\\\"/" Documentation/html/index.html

test:
	cd Tests; $(MAKE) $(MFLAGS)

release: clean jshint min jsmin
	mkdir Release
	tar cfz Release/depage-forms.tar.gz README.md Abstracts Documentation/examples Elements Exceptions Validators lib/js/*.min.js lib/css lib/*.png HtmlForm.php composer.json
	zip -r release/depage-forms.zip README.md Abstracts Documentation/examples Elements Exceptions Validators lib/js/*.min.js lib/css lib/*.png HtmlForm.php composer.json
	shasum -a 512 Release/depage-forms.zip > Release/depage-forms.zip.sha2
	shasum -a 512 Release/depage-forms.tar.gz > Release/depage-forms.tar.gz.sha2

min: clean jsmin
	tar cfz depage-forms.tar.gz Abstracts Elements Exceptions Validators lib/js/*.min.js HtmlForm.php
	sha512sum depage-forms.tar.gz > depage-forms.tar.gz.sha2

jsmin:
	cd lib/js; $(MAKE) $(MFLAGS) jsmin

jshint:
	cd lib/js; $(MAKE) $(MFLAGS) jshint

clean:
	$(RM) Documentation/depage-docu/ Documentation/html/
	cd Tests; $(MAKE) $(MFLAGS) clean
	${RM} Release

.PHONY: all
.PHONY: clean
.PHONY: test
.PHONY: doc

