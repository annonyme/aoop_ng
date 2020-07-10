#FILE=/var/www/html/vendor/autoload.php
#if test -f "$FILE"; then
#    php /var/www/html/utils/composer.phar update
#else
#    php /var/www/html/utils/composer.phar install
#fi
/usr/sbin/apache2ctl -D FOREGROUND