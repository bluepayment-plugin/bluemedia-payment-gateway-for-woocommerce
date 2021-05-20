FROM harbour-ndi.bsbox.pl/bs_ecommerce/woocommerce/core:php7.3 as builder

ENV TZ=Europe/Warsaw
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY --chown=www-data:www-data /bluepayment /var/www/html/wp-content/plugins/bluepayment
COPY --chown=www-data:www-data /docker/build/wp-content/languages /var/www/html/wp-content/languages

FROM builder as local

#COPY docker wp-config.php /var/www/html odkomentowac po dodaniu harbora

FROM builder as test

COPY --chown=www-data:www-data /k8s/wp-config.php /var/www/html

RUN mkdir -p /wp-content/uploads/wc-logs/
RUN ls -la /wp-content/uploads/
RUN chmod -R 777 /wp-content/uploads

