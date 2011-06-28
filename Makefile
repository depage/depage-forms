RM = rm -rf

all: doc test

doc:
	cd documentation; $(MAKE) $(MFLAGS)

test:
	cd tests; $(MAKE) $(MFLAGS)

release: clean
	tar cfz depage-forms.tar.gz abstracts documentation/examples elements exceptions validators lib htmlform.php
	md5sum depage-forms.tar.gz > depage-forms.tar.gz.md5
	sha512sum depage-forms.tar.gz > depage-forms.tar.gz.sha2

min: clean
	tar cfz depage-forms.tar.gz abstracts elements exceptions validators lib htmlform.php
	md5sum depage-forms.tar.gz > depage-forms.tar.gz.md5
	sha512sum depage-forms.tar.gz > depage-forms.tar.gz.sha2

clean:
	cd documentation; $(MAKE) $(MFLAGS) clean
	cd tests; $(MAKE) $(MFLAGS) clean
	${RM} depage-forms.tar.gz*

.PHONY: all
.PHONY: clean
.PHONY: test
.PHONY: doc

