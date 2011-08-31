jsmin:
	curl -f -X POST --data-urlencode js_code@depage-richtext.js -o depage-richtext.min.js http://marijnhaverbeke.nl/uglifyjs || true
	curl -f -X POST --data-urlencode js_code@effect.js -o effect.min.js http://marijnhaverbeke.nl/uglifyjs || true

jslint:
	curl -s -F js=@depage-richtext.js http://jslint.webvm.net/ | sed 's,^,'"depage-richtext.js "','
	curl -s -F js=@effect.js http://jslint.webvm.net/ | sed 's,^,'"effect.js:"','
