FROM php:7.4-cli
COPY . /usr/src/quickrouter
WORKDIR /usr/src/quickrouter
RUN apt-get -y update
RUN apt-get -y install git
RUN apt-get install zip unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install