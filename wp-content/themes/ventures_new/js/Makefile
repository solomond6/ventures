.PHONY: checkUglify

all: checkUglify main.desktop.min.js main.mobile.min.js

checkUglify:
	@which uglifyjs > /dev/null || ( echo "UglifyJS is missing." && false )

main.desktop.min.js: vendor/*.js Scroll.js centered-popup.js blur-canvas.js main.desktop.js
	uglifyjs vendor/picturefill.js \
			 Scroll.js \
			 vendor/jquery.waypoints.js \
			 centered-popup.js \
			 vendor/jquery.dfp.js \
			 vendor/jquery.html5-placeholder-shim.js \
			 blur-canvas.js \
			 main.desktop.js \
		--compress drop_console=true,warnings=false \
		--mangle \
		--output main.desktop.min.js

main.mobile.min.js: vendor/*.js touch-events.js animation.js Scroll.js main.mobile.js
	uglifyjs vendor/underscore.js \
			 touch-events.js \
			 animation.js \
			 Scroll.js \
			 vendor/picturefill.js \
			 vendor/jquery.dfp.js \
			 main.mobile.js \
		--compress drop_console=true,warnings=false \
		--mangle \
		--output main.mobile.min.js
