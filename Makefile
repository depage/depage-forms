RM = rm -rf

all: test doc

doc:
	cd documentation; $(MAKE) $(MFLAGS)

test:
	cd tests; $(MAKE) $(MFLAGS)

release: clean jsmin
	mkdir release
	tar cfz release/depage-forms.tar.gz README.md abstracts documentation/examples elements exceptions validators lib/js/*.min.js lib/css lib/*.png htmlform.php
	zip -r release/depage-forms.zip README.md abstracts documentation/examples elements exceptions validators lib/js/*.min.js lib/css lib/*.png htmlform.php
	md5sum release/depage-forms.zip > release/depage-forms.zip.md5
	shasum -a 512 release/depage-forms.zip > release/depage-forms.zip.sha2
	md5sum release/depage-forms.tar.gz > release/depage-forms.tar.gz.md5
	shasum -a 512 release/depage-forms.tar.gz > release/depage-forms.tar.gz.sha2

min: clean jsmin
	tar cfz depage-forms.tar.gz abstracts elements exceptions validators lib/js/*.min.js htmlform.php
	md5sum depage-forms.tar.gz > depage-forms.tar.gz.md5
	sha512sum depage-forms.tar.gz > depage-forms.tar.gz.sha2

jsmin:
	cd lib/js; $(MAKE) $(MFLAGS) jsmin

jslint:
	cd lib/js; $(MAKE) $(MFLAGS) jslint

clean:
	cd documentation; $(MAKE) $(MFLAGS) clean
	cd tests; $(MAKE) $(MFLAGS) clean
	${RM} release

.PHONY: all
.PHONY: clean
.PHONY: test
.PHONY: doc

