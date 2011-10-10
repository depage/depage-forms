jsmin:
	curl -s \
        -d compilation_level=SIMPLE_OPTIMIZATIONS \
        -d output_format=text \
        -d output_info=compiled_code \
        --data-urlencode "js_code@depage-richtext.js" \
        http://closure-compiler.appspot.com/compile \
        > depage-richtext.min.js
	curl -s \
        -d compilation_level=SIMPLE_OPTIMIZATIONS \
        -d output_format=text \
        -d output_info=compiled_code \
        --data-urlencode "js_code@effect.js" \
        http://closure-compiler.appspot.com/compile \
        > effect.min.js

jslint:
	curl -s -F js=@depage-richtext.js http://jslint.webvm.net/ | sed 's,^,'"depage-richtext.min.js "','
	curl -s -F js=@effect.js http://jslint.webvm.net/ | sed 's,^,'"effect.min.js:"','
