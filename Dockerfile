# Debian: NGINX + PHP5-FPM + Composer + MySQL
#
# VERSION 0.0.1

# Pull the base image.
FROM debian:jessie

# Set the author.
MAINTAINER Eric Mugerwa <dev@ericmugerwa.com>

# Set environment variables.
ENV FILES 		   provision/files
ENV SCRIPTS		   provision/scripts
ENV WEBAPP         /srv/www/webapp
ENV TIMEZONE	   Europe/London
ENV NGINX_VERSION  1.9.9-1~jessie
ENV MYSQL_VERSION  5.5
ENV MYSQL_PASSWORD password

# Timezone
RUN echo "${TIMEZONE}" > /etc/timezone
RUN dpkg-reconfigure -f noninteractive tzdata

# Essentials
RUN apt-get update && \
    apt-get install -y \
    curl \
    git \
    nano \
    wget \
    zsh \
    htop \
    expect \
    supervisor

# SSHD (Password = 'admin')
RUN apt-get update && \
    apt-get install -y openssh-server
RUN mkdir /var/run/sshd
RUN printf admin\\nadmin\\n | passwd

# NGINX
RUN apt-key adv --keyserver hkp://pgp.mit.edu:80 --recv-keys 573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62
RUN echo "deb http://nginx.org/packages/mainline/debian/ jessie nginx" >> /etc/apt/sources.list
RUN apt-get update && \
    apt-get install -y openssl ca-certificates nginx=${NGINX_VERSION}

# PHP 5.6
RUN apt-get clean && \
	apt-get update && \
    apt-get install -y \
    php5-apcu \
    php5-cli \
    php5-common \
    php5-curl \
    php5-fpm \
    php5-mysql

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
	composer --version

# MySQL 5.6
RUN export DEBIAN_FRONTEND="noninteractive"
RUN echo mysql-server mysql-server/root_password password ${MYSQL_PASSWORD} | debconf-set-selections
RUN echo mysql-server mysql-server/root_password_again password ${MYSQL_PASSWORD} | debconf-set-selections
RUN apt-get update && \
	apt-get install -y mysql-server-${MYSQL_VERSION}

# Clean up APT when done.
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure Supervisor.
ADD ${FILES}/supervisord.conf /etc/supervisor/conf.d/

# Configure NGINX.
RUN rm -rf /etc/nginx/nginx.conf
RUN rm -rf /etc/nginx/conf.d/*
RUN rm -rf /var/www/*
RUN rm -rf /srv/www/*
ADD ${FILES}/nginx.conf /etc/nginx/
ADD ${FILES}/webapp.conf /etc/nginx/conf.d/
ADD ${FILES}/fastcgi_params /etc/nginx/
ADD ${FILES}/php-upstream.conf /etc/nginx/conf.d/upstream.conf

# Forward request and error logs to docker log collector.
RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

# Configure PHP.
ADD ${FILES}/webapp.ini /etc/php5/fpm/conf.d/
ADD ${FILES}/webapp.ini /etc/php5/cli/conf.d/
ADD ${FILES}/webapp.pool.conf /etc/php5/fpm/pool.d/

# Configure MySQL.
RUN rm -rf /etc/mysql/my.cnf
ADD ${FILES}/my.cnf /etc/mysql/

# Configure Symfony - chown.
RUN mkdir -p /srv/www/webapp/var/cache/prod/; \
    mkdir -p /srv/www/webapp/var/logs/; \
    mkdir -p /srv/www/webapp/var/sessions/prod/annotations; \
    chmod -R 777 /srv/www/webapp/var/cache/; \
    chmod -R 777 /srv/www/webapp/var/logs/; \
    chmod -R 777 /srv/www/webapp/var/sessions/

# Add scripts
ADD ${SCRIPTS}/app/deploy.sh /opt/app/
ADD ${SCRIPTS}/app/clear_cache.sh /opt/app/
ADD ${SCRIPTS}/app/debug.sh /opt/app/

# Define mountable directories
VOLUME ["/srv/www", "/var/log/nginx/"]

# Expose ports: HTTP - HTTPS - SSH - MySQL.
EXPOSE 80
EXPOSE 443
EXPOSE 22
EXPOSE 3306

# Configure executable.
ENTRYPOINT ["/usr/bin/supervisord"]

# Define default command.
CMD []
