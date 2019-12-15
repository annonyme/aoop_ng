FROM php:7.2-apache

RUN apt-get update &&\
    apt-get install --no-install-recommends --assume-yes --quiet ca-certificates \ 
    curl \
    git \
    libxml2-dev \
    libxslt-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libcurl4-gnutls-dev \
    zlib1g-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install json \
    && docker-php-ext-install dom \
    && docker-php-ext-install curl \
    && docker-php-ext-install zip \
    && docker-php-ext-install xml
RUN rm -rf /var/lib/apt/lists/*



RUN pecl install xdebug-2.8.0 && docker-php-ext-enable xdebug
RUN echo 'zend_extension="/usr/local/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so"' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.remote_port=9000' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.remote_host=host.docker.internal' >> /usr/local/etc/php/php.ini

RUN a2enmod rewrite

# copy conf-file to /etc/apache2/sites-enabled/000-default.conf
RUN mkdir /files;
COPY ./setup.sh /files/setup.sh
ENTRYPOINT ["sh", "/files/setup.sh"]