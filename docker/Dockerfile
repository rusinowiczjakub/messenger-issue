# BASE:
FROM php:8.1-fpm AS base
WORKDIR /cropink
ENV LOCALTIME UTC
RUN apt-get update --fix-missing -y \
    && apt-get upgrade -y \
    && apt-get install -y \
        build-essential \
        acl \
        git \
        libicu-dev \
        libpq-dev \
        libz-dev \
        libzip-dev \
        libssh2-1 \
        libssh2-1-dev \
        librabbitmq-dev \
        zip \
        unzip \
        wget \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure bcmath \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure sockets \
    && docker-php-ext-install \
        bcmath \
        intl \
        pdo \
        pdo_pgsql \
        pgsql \
        sockets \
        zip \
    && pecl channel-update pecl.php.net \
    && pecl install ssh2-1.3.1 \
    && pecl install ssh2-1.3.1 amqp \
    && docker-php-ext-enable ssh2 \
    && docker-php-ext-enable amqp \
    && ln -sf /usr/share/zoneinfo/$LOCALTIME /etc/localtime \
    && echo "date.timezone = \"${LOCALTIME}\"" | tee --append /usr/local/etc/php/conf.d/00-default.ini
COPY ./docker/php/php-fpm.conf "/usr/local/etc/php-fpm.d/01-app.conf"
RUN wget https://getcomposer.org/installer --quiet --output-document=/tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-setup.php

# PROD:
FROM base AS prod
ENV APP_ENV=prod
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/01-app.ini
COPY . ./
RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer install --no-cache --prefer-dist --no-dev --no-interaction --optimize-autoloader --no-scripts; \
    composer dump-env prod; \
#    composer run-script --no-dev post-install-cmd; \
    chmod +x bin/console; \
    sync;
COPY ./docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod 0755 /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# DEV:
FROM base AS dev

ARG USER_NAME=www-data
ARG GROUP_NAME=www-data
ARG USER_UID=1000
ARG USER_GID=1000

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY ./docker/php/php.dev.ini /usr/local/etc/php/conf.d/01-app.ini

# only for deptrac to generate the result of the analysis
RUN apt-get update \
    && apt-get install -y graphviz \
    && rm -rf /var/lib/apt/lists/*

RUN groupmod --non-unique --gid $USER_GID $GROUP_NAME \
    && usermod --uid $USER_UID --gid $USER_GID --shell /bin/bash $USER_NAME

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=on" >> /usr/local/etc/php/conf.d/xdebug.ini
