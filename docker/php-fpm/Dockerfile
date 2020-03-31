FROM alpine

RUN \
    apk update \
    \
    # install php
    && apk add php7 \
    && apk add php7-amqp \
    && apk add php7-apcu \
    && apk add php7-bcmath \
    && apk add php7-ctype \
    && apk add php7-curl \
    && apk add php7-dom \
    && apk add php7-fileinfo \
    && apk add php7-iconv \
    && apk add php7-intl \
    && apk add php7-json \
    && apk add php7-mbstring \
    && apk add php7-mcrypt \
    && apk add php7-mysqlnd \
    && apk add php7-opcache \
    && apk add php7-openssl \
    && apk add php7-pdo \
    && apk add php7-pdo_mysql \
    && apk add php7-phar \
    && apk add php7-posix \
    && apk add php7-redis \
    && apk add php7-session \
    && apk add php7-simplexml \
    && apk add php7-tokenizer \
    && apk add php7-xdebug \
    && apk add php7-xml \
    && apk add php7-fpm \
    && apk add openssl \
    \
    # remove caches to decrease image size
    && rm -rf /var/cache/apk/* \
    \
    # install composer
    && EXPECTED_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig) \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '$EXPECTED_SIGNATURE') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz

ENV PHP_INI_DIR /etc/php7
ENV PHP_FPMD_DIR /etc/php7/php-fpm.d

COPY php.ini $PHP_INI_DIR/

RUN \
    # add non-root user
    adduser -D nonroot nonroot \
    \
    # to update conf files and create temp files under the directory via sed command on runtime
    && chmod -R a+w /etc/php7/php-fpm.d \
    \
    # add nonroot to sudoers
    && apk add --update sudo \
    && echo "nonroot ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

COPY www.conf $PHP_FPMD_DIR/

ENV DOCROOT /home/nonroot/payments_api

WORKDIR $DOCROOT

CMD ["/usr/sbin/php-fpm7", "--nodaemonize"]

