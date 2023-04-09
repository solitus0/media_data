FROM php:8.1-fpm-alpine AS builder

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions pdo_mysql ctype curl dom filter iconv intl json libxml mbstring pcre phar simplexml tokenizer xml xmlwriter xsl zip xdebug sysvsem

RUN apk --update --no-cache add git \
    && apk add vim \
    && apk add bash \
    && apk add zsh \
    && apk add fontconfig \
    && apk add yarn \
    && apk add npm \
    && apk add openssh \
    && apk add icu-dev \
    && apk add libxml2-dev \
    && apk add busybox-extras \
    && apk add coreutils \
    && apk add --no-cache zip libzip-dev \
    && apk add nginx

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

RUN rm -rf /tmp/*

RUN curl -L -sS --output local-php-security-checker https://github.com/fabpot/local-php-security-checker/releases/download/v1.0.0/local-php-security-checker_1.0.0_linux_amd64 \
    && chmod a+x local-php-security-checker \
    && mv local-php-security-checker /usr/local/bin/local-php-security-checker

RUN sh -c "$(wget -O- https://github.com/deluan/zsh-in-docker/releases/download/v1.1.1/zsh-in-docker.sh)" -- \
    -p git \
    -p ssh-agent \
    -p https://github.com/zsh-users/zsh-autosuggestions \
    -p https://github.com/zsh-users/zsh-completions

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh \
    && apk add symfony-cli

COPY ./docker/app/nginx/nginx.conf /etc/nginx/
COPY ./docker/app/nginx/sites/default.conf /etc/nginx/sites-available/default.conf
COPY ./docker/app/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker/app/nginx/entrypoint.sh /etc/entrypoint.sh
COPY ./docker/app/php/php.ini /usr/local/etc/php/conf.d/zz-php.ini
COPY ./docker/app/php/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./docker/app/zsh/.p10k.zsh /root/.p10k.zsh

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log
RUN chmod +x /etc/entrypoint.sh

RUN chown -R www-data:www-data /var/lib/nginx

FROM builder AS app

WORKDIR /var/www/api/current

RUN git -C /var/www/api/current/ clone https://github.com/solitus0/media_data.git .
RUN chown -R www-data:www-data /var/www/api/current
RUN composer install --no-interaction

EXPOSE 80

ENTRYPOINT ["/etc/entrypoint.sh"]
