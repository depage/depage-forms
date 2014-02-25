RM = rm -rf

all: test doc

doc:
	cd documentation; $(MAKE) $(MFLAGS)

docset:
	cd documentation; $(MAKE) $(MFLAGS) docset

test:
	cd tests; $(MAKE) $(MFLAGS)

release: clean jshint min jsmin
	mkdir release
	tar cfz release/depage-forms.tar.gz README.md abstracts documentation/examples elements exceptions validators lib/js/*.min.js lib/css lib/*.png htmlform.php composer.json
	zip -r release/depage-forms.zip README.md abstracts documentation/examples elements exceptions validators lib/js/*.min.js lib/css lib/*.png htmlform.php composer.json
	shasum -a 512 release/depage-forms.zip > release/depage-forms.zip.sha2
	shasum -a 512 release/depage-forms.tar.gz > release/depage-forms.tar.gz.sha2

min: clean jsmin
	tar cfz depage-forms.tar.gz abstracts elements exceptions validators lib/js/*.min.js htmlform.php
	sha512sum depage-forms.tar.gz > depage-forms.tar.gz.sha2

jsmin:
	cd lib/js; $(MAKE) $(MFLAGS) jsmin

jshint:
	cd lib/js; $(MAKE) $(MFLAGS) jshint

clean:
	cd documentation; $(MAKE) $(MFLAGS) clean
	cd tests; $(MAKE) $(MFLAGS) clean
	${RM} release

.PHONY: all
.PHONY: clean
.PHONY: test
.PHONY: doc

