FROM ubuntu:14.04

# Requirements
RUN apt-get update && apt-get install -y php5 php5-curl php5-sqlite curl git apache2

# Set up baikal
WORKDIR /var/www
RUN git clone -b branch-2 https://github.com/netgusto/Baikal.git baikal
RUN chown -R www-data:www-data baikal
RUN chmod -R 775 baikal

WORKDIR /var/www/baikal
RUN cp app/config/defaults/data.parameters.dist.yml data/parameters.yml
RUN cp app/config/defaults/data.environment.dist.yml data/environment.yml
RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install --optimize-autoloader

WORKDIR /var/www/baikal/app/cache
RUN mkdir prod

WORKDIR /var/www
RUN chown -R www-data:www-data baikal
RUN chmod -R 775 baikal

# apache2 conf
RUN rm /etc/apache2/sites-enabled/000-default.conf
COPY baikal.conf /etc/apache2/sites-available/
RUN ln -s /etc/apache2/sites-available/baikal.conf /etc/apache2/sites-enabled/baikal.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 8000
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
