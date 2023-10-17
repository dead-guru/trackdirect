FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
  git \
  libpq-dev \
  postgresql-client-common \
  postgresql-client \
  libevent-dev \
  libmagickwand-dev \
  imagemagick \
  inkscape \
  && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

#COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN pecl install imagick && docker-php-ext-enable imagick && docker-php-ext-install pdo pdo_pgsql && docker-php-ext-install gd && docker-php-ext-enable gd

COPY . /root/trackdirect
COPY config/000-default.conf /etc/apache2/sites-enabled/

RUN a2enmod rewrite
RUN chmod a+rx / && chmod a+rx -R /root
RUN chmod 777 /root/trackdirect/htdocs/public/symbols
RUN chmod 777 /root/trackdirect/htdocs/public/heatmaps

