#COTECMAR DOCKERFILE
#COTECMAR
FROM php:8.2.0-apache
ARG XDEBUG_VERSION="xdebug-3.3.1"
ENV TZ=America/Bogota
USER root
ENV LANGUAGE=es_CO
ENV LC_ALL=es_CO.UTF-8
ENV LANG=es_CO.UTF-8

WORKDIR /var/www/html

RUN yes | pecl install ${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug

RUN apt-get update && apt-get install -y locales && apt-get install language-pack-es
RUN locale-gen es_CO es_CO.UTF-8

RUN dpkg-reconfigure locales


RUN docker-php-ext-install gettext

RUN apt update && apt install -y default-jre


RUN apt update; \
    apt install -y libmagickwand-dev; \
    pecl install imagick; \
    docker-php-ext-enable imagick

RUN apt update &&  apt install -y -f \
        git \
        gnupg2 curl \
        apt-utils \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        libbz2-dev \
        libcurl3-openssl-dev \
        libgmp-dev \
        libldap2-dev \
        libxslt-dev \
        libsqlite3-dev \
        openssl \
        libc-client-dev libkrb5-dev && rm -r /var/lib/apt/lists/* \
    && docker-php-ext-install bz2 \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install curl \
    && docker-php-ext-install exif \
    && docker-php-ext-install fileinfo \
    && docker-php-ext-install gd \
    && docker-php-ext-install gettext \
    && docker-php-ext-install gmp \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap \
    && docker-php-ext-install intl \
    && docker-php-ext-install ldap \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install soap \
    && docker-php-ext-install xsl \
    && docker-php-ext-install pdo_sqlite \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip

RUN mkdir -p /etc/apache2/certificate

RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && \
    ACCEPT_EULA=Y apt-get install -y msodbcsql17 odbcinst=2.3.7 odbcinst1debian2=2.3.7 unixodbc-dev=2.3.7 unixodbc=2.3.7 && \
    pecl install sqlsrv pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv

COPY . /var/www/html/PROJECT/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite && a2enmod ssl

USER www-data
