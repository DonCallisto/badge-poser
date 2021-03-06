FROM node:14-alpine AS jsbuilder

WORKDIR /application

COPY . /application

RUN apk add --no-cache --virtual frontenddeps \
       python3 \
       yarn \
    && npm install -g --unsafe-perm node-sass \
    && yarn config set ignore-engines true \
    && yarn install --production \
    && yarn add @symfony/webpack-encore --production \
    && yarn add sass-loader@^8.0.0 node-sass --dev \
    && npm run build

FROM php:7.4-fpm-alpine

RUN apk add --no-cache --virtual .builddeps \
        autoconf \
        g++ \
        gcc \
        git \
        make \
        unzip \
        wget \
    && apk add --no-cache \
        freetype-dev \
        icu-dev \
        jpeg-dev \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        yaml-dev \
        zlib-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        zip \
    && pecl install msgpack yaml redis \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY sys/docker/alpine-phpfpm/php.ini /usr/local/etc/php/php.ini
COPY sys/docker/alpine-phpfpm/docker-php-ext-opcache.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
COPY sys/docker/alpine-phpfpm/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY sys/docker/alpine-phpfpm/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /application

COPY . /application
COPY --from=jsbuilder /application/public/build /application/public/build

# REF: https://github.com/api-platform/api-platform/issues/1532
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet \
    && mv composer.phar /usr/local/bin/composer \
    && /usr/local/bin/composer global require hirak/prestissimo \
    && /usr/local/bin/composer install --no-plugins --no-scripts --optimize-autoloader --no-ansi --no-interaction --no-progress --no-suggest \
    && apk del .builddeps \
    && php-fpm -t \
    && php-fpm -tt

COPY sys/docker/alpine-phpfpm/start.sh /application/start.sh
CMD ["/application/start.sh"]
