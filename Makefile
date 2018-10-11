RM = rm -rf

all: test doc

doc:
	cd Docs ; git clone https://github.com/depage/depage-docu.git depage-docu || true
	doxygen Docs/Doxyfile
	cp -r Docs/depage-docu/www/lib Docs/html/

test:
	cd Tests; $(MAKE) $(MFLAGS)

release: clean jshint min jsmin
	mkdir Release
	tar cfz Release/depage-forms.tar.gz README.md Abstracts Docs/examples Elements Exceptions Validators lib/js/*.min.js lib/css lib/*.png HtmlForm.php composer.json
	zip -r release/depage-forms.zip README.md Abstracts Docs/examples Elements Exceptions Validators lib/js/*.min.js lib/css lib/*.png HtmlForm.php composer.json
	shasum -a 512 Release/depage-forms.zip > Release/depage-forms.zip.sha2
	shasum -a 512 Release/depage-forms.tar.gz > Release/depage-forms.tar.gz.sha2

min: clean jsmin

jsmin:
	cd lib/js; $(MAKE) $(MFLAGS) jsmin

jshint:
	cd lib/js; $(MAKE) $(MFLAGS) jshint

clean:
	$(RM) Docs/depage-docu/ Docs/html/
	cd Tests; $(MAKE) $(MFLAGS) clean
	${RM} Release

.PHONY: all
.PHONY: clean
.PHONY: test
.PHONY: doc

