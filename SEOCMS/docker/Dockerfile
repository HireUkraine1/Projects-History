FROM php:7.0-fpm

RUN apt-get update

# Install Postgre PDO, bcmath
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath zip

#Composer install
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
&& rm -f /tmp/composer-setup.*

RUN curl -sL https://deb.nodesource.com/setup_6.x | bash - \
&& apt-get -y install nodejs \
&& apt-get -y install libfontconfig \
&& npm install --save critical -g

#Install supervisord and configure laravel queues
RUN apt-get install -y supervisor
COPY ./supervisord/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

CMD service supervisor start ; php-fpm