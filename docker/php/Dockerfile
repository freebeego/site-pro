FROM php:8.2-fpm

ARG MODE

WORKDIR /var/www

RUN if [ "$MODE" = "prod" ] ; \
    then mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
    else pecl install xdebug && docker-php-ext-enable xdebug ; \
    fi

RUN docker-php-ext-install mysqli opcache && docker-php-ext-enable opcache

ARG HOST_USER_ID
ARG HOST_GROUP_ID

RUN if [ ! -z "$HOST_USER_ID" ] ; then usermod -u $HOST_USER_ID www-data ; fi
RUN if [ ! -z "$HOST_GROUP_ID" ] ; then groupmod -g $HOST_GROUP_ID www-data ; fi
