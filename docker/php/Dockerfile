FROM php:7.1-apache

# Basic installation
RUN apt-get update && apt-get install -y -qq libssl-dev cmake libxml2-dev curl nano git zip unzip libevent-dev sqlite3 libev-dev wget

# Virtualhost
COPY workshop.app.conf /etc/apache2/sites-enabled/000-default.conf

RUN a2enmod proxy_http headers

# RabbitMQ C Library
RUN curl -L -o /tmp/amqp.tar.gz https://github.com/alanxz/rabbitmq-c/releases/download/v0.8.0/rabbitmq-c-0.8.0.tar.gz \
  && tar xfz /tmp/amqp.tar.gz \
  && rm -r /tmp/amqp.tar.gz \
  && mkdir -p /usr/src/php/ext \
  && mv rabbitmq-c-0.8.0 /usr/src/php/ext/amqp \
  && cd /usr/src/php/ext/amqp \
  && ./configure \
  && cmake -ENABLE_SSL_SUPPORT=ON . \
  && cmake --build . --target install

# Redis
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/3.0.0.tar.gz \
  && tar xfz /tmp/redis.tar.gz \
  && rm -r /tmp/redis.tar.gz \
  && mkdir -p /usr/src/php/ext \
  && mv phpredis-3.0.0 /usr/src/php/ext/redis \
  && cd /usr/src/php/ext/redis \
  && phpize \
  && ./configure \
  && make && make install

# AMQP
RUN pecl install amqp-1.9.0

# Enable PHP extensions
RUN docker-php-ext-enable amqp redis

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer