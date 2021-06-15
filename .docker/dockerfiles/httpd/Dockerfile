FROM httpd:2.4

ADD ./conf/vhost-application.conf /usr/local/apache2/conf/custom/vhost-application.conf

RUN echo "Include conf/custom/vhost-application.conf" >> /usr/local/apache2/conf/httpd.conf

ENV TZ Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var/www
