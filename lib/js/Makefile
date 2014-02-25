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

jshint:
	jshint depage-richtext.js
	jshint effect.js 
