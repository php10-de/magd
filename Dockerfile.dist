FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip wget

# Install IonCube Loader
RUN wget https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz && \
    tar xfz ioncube_loaders_lin_x86-64.tar.gz && \
    cp ioncube/ioncube_loader_lin_8.1.so /usr/local/lib/php/extensions/no-debug-non-zts-*/ && \
    echo "zend_extension=ioncube_loader_lin_8.2.so" > /usr/local/etc/php/conf.d/00-ioncube.ini && \
    rm -rf ioncube*

RUN docker-php-ext-install mysqli

EXPOSE 80
