RM = rm -rf

all: test doc

doc:
	cd documentation; $(MAKE) $(MFLAGS)

test:
	cd tests; $(MAKE) $(MFLAGS)

release: clean jsmin
	tar cfz depage-forms.tar.gz abstracts documentation/examples elements exceptions validators lib/js/*.min.js htmlform.php
	md5sum depage-forms.tar.gz > depage-forms.tar.gz.md5
	sha512sum depage-forms.tar.gz > depage-forms.tar.gz.sha2

min: clean jsmin
	tar cfz depage-forms.tar.gz abstracts elements exceptions validators lib/js/*.min.js htmlform.php
	md5sum depage-forms.tar.gz > depage-forms.tar.gz.md5
	sha512sum depage-forms.tar.gz > depage-forms.tar.gz.sha2

jsmin:
	curl -f -X POST --data-urlencode js_code@lib/js/effect.js -o lib/js/effect.min.js http://marijnhaverbeke.nl/uglifyjs || true

clean:
	cd documentation; $(MAKE) $(MFLAGS) clean
	cd tests; $(MAKE) $(MFLAGS) clean
	${RM} depage-forms.tar.gz*

.PHONY: all
.PHONY: clean
.PHONY: test
.PHONY: doc

