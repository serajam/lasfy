 #!/bin/sh
if [ -e ../public_html/css/min_styles.css ]; then rm ../public_html/css/min_styles.css; fi
cat  ../public_html/foundation/foundation.min.css ../public_html/foundation/normalize.css ../public_html/foundation/icons/foundation-icons.css ../public_html/css/jquery.tag-editor.css ../public_html/css/style.css ../public_html/css/jquery.mCustomScrollbar.css > ../public_html/css/min_styles.css
java -jar yuicompressor-2.4.8.jar ../public_html/css/min_styles.css -o ../public_html/css/min_styles.css

if [ -e ../public_html/js/client/scripts.min.js ]; then rm ../public_html/js/client/scripts.min.js; fi
cat  ../public_html/js/jquery-1.11.1.min.js ../public_html/foundation/foundation.min.js ../public_html/foundation/modernizr.js ../public_html/js/plugins/tagEditor/jquery.caret.min.js ../public_html/js/plugins/tagEditor/jquery.tag-editor.js ../public_html/js/client/jquery.ez-bg-resize.js ../public_html/js/client/jquery.orbit-1.2.3.min.js ../public_html/js/client/jquery.mCustomScrollbar.min.js ../public_html/js/client/jquery.mousewheel.min.js ../public_html/js/plugins/ajaxModalUrl.js ../public_html/js/plugins/ajax.modalForm.submitter.js ../public_html/js/plugins/ajax.form.submitter.js ../public_html/js/plugins/modalMessage.js ../public_html/js/plugins/tagsSearchWorker.js ../public_html/js/plugins/opft.js ../public_html/js/jquery.form.js ../public_html/js/client/basic.js ../public_html/js/jquery.countdown.js  > ../public_html/js/client/scripts.min.js
java -jar yuicompressor-2.4.8.jar ../public_html/js/client/scripts.min.js -o ../public_html/js/client/scripts.min.js
