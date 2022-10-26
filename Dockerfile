FROM php:8.0-fpm
ENV COMPOSER_VERSION=2.0.11
RUN apt-get update && apt-get install -y wget git
RUN wget https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar -O /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    && composer --version

RUN apt-get update \
  && apt-get install -y \
             apt-utils \
             man \
             curl \
             git \
             bash \
             vim \
             zip unzip \
             acl \
             iproute2 \
             dnsutils \
             fonts-freefont-ttf \
             fontconfig \
             dbus \
             openssh-client \
             sendmail \
             libfreetype6-dev \
             libjpeg62-turbo-dev \
             icu-devtools \
             libicu-dev \
             libmcrypt4 \
             libmcrypt-dev \
             libpng-dev \
             zlib1g-dev \
             libxml2-dev \
             libzip-dev \
             libonig-dev \
             graphviz \
             libcurl4-openssl-dev \
             pkg-config \
             libldap2-dev \
             libpq-dev \
  && pecl install mongodb \
  && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb-ext.ini


RUN docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install pdo \
        pgsql pdo_pgsql \
        mysqli pdo_mysql \
        intl iconv mbstring \
        zip pcntl \
        exif opcache \
    && docker-php-source delete

RUN docker-php-ext-install exif gd

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

ENTRYPOINT ["./entrypoint.sh"]
