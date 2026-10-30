RM = rm -rf
VERSION = $(shell  git describe --tags --abbrev=0)

all: test doc

doc:
	cd Docs ; git clone https://github.com/depage/depage-docu.git depage-docu || true
	( cat Docs/Doxyfile ; echo "PROJECT_NUMBER=$(VERSION)" ) | doxygen -

test:
	cd Tests; $(MAKE) $(MFLAGS)

release: clean jsmin
	mkdir -p Release
	tar cfz Release/depage-forms.tar.gz README.md Abstracts Docs/examples Elements Exceptions Validators dist HtmlForm.php composer.json package.json
	zip -r Release/depage-forms.zip README.md Abstracts Docs/examples Elements Exceptions Validators dist HtmlForm.php composer.json package.json
	shasum -a 512 Release/depage-forms.zip > Release/depage-forms.zip.sha2
	shasum -a 512 Release/depage-forms.tar.gz > Release/depage-forms.tar.gz.sha2

min: clean jsmin

# builds the javascript bundles and the css from src/ into dist/
jsmin:
	bun install
	bun run build

jstest:
	bun test src/tests

clean:
	$(RM) Docs/depage-docu/ Docs/html/
	cd Tests; $(MAKE) $(MFLAGS) clean
	${RM} Release

.PHONY: all
.PHONY: clean
.PHONY: test
.PHONY: doc

