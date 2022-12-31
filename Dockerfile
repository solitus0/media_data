FROM php:8.1-fpm-alpine

ENV APP_ENV=prod

RUN apk --update --no-cache add git \
    && apk add doas \
    && apk add vim \
    && apk add git \
    && apk add zsh \
    && apk add fontconfig \
    && apk add icu-dev \
    && apk add libxml2-dev \
    && apk add busybox-extras \
    && apk add coreutils \
    && apk add --no-cache zip libzip-dev

RUN sh -c "$(wget -O- https://github.com/deluan/zsh-in-docker/releases/download/v1.1.1/zsh-in-docker.sh)" -- \
    -p git \
    -p ssh-agent \
    -p https://github.com/zsh-users/zsh-autosuggestions \
    -p https://github.com/zsh-users/zsh-completions

COPY ./docker/php/config/.p10k.zsh /root/.p10k.zsh
COPY ./docker/php/config/.zshrc /root/.zshrc
COPY ./docker/php/config/php.ini /usr/local/etc/php/conf.d/zz-php.ini
COPY ./docker/php/config/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN echo 'pm.max_children = 15' >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo 'pm.max_requests = 500' >> /usr/local/etc/php-fpm.d/zz-docker.conf

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh \
    && apk add symfony-cli

RUN mkdir -p /var/www/api/current \
    && mkdir -p /var/www/api/var/log \
    && mkdir -p /var/www/api/shared

RUN git -C /var/www/api/current/ clone https://github.com/solitus0/media_data.git .
RUN chown -R www-data:www-data /var/www/api/current

ENV PATH=$PATH:/root/composer2/vendor/bin \
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/root/composer2

RUN cd /opt \
  && curl -sSL https://getcomposer.org/installer > composer-setup.php \
  && curl -sSL https://composer.github.io/installer.sha384sum > composer-setup.sha384sum \
  && sha384sum --check composer-setup.sha384sum \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer2 --2 \
  && ln -s /usr/local/bin/composer2 /usr/local/bin/composer \
  && rm /opt/composer-setup.php /opt/composer-setup.sha384sum

RUN cd /var/www/api/current \
    && APP_ENV=prod /usr/local/bin/composer install --no-dev --verbose --prefer-dist --optimize-autoloader --no-progress \
    && rm -rf /tmp/* \
    && chown -R www-data:www-data *

WORKDIR /var/www/api/current

CMD symfony serve -d --no-tls --port=8090 ; php-fpm ;

EXPOSE 8090
