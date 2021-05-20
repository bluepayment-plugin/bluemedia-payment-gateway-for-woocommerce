FROM wordpress:php7.3

# install curl and jq
RUN apt update && apt install -y jq curl

# install latest release of woocommerce from github
RUN mkdir /usr/src/wordpress/wp-content/plugins/woocommerce \
  && curl -sL $(curl -sL -H "Accept: application/vnd.github.v3.full+json" \
  https://api.github.com/repos/woocommerce/woocommerce/releases/tags/4.1.0 \
  | jq -r .tarball_url) \
  | tar zx -C /usr/src/wordpress/wp-content/plugins/woocommerce \--strip-components 1

COPY wp-config.php /var/www/html

RUN mkdir -p /usr/src/wordpress/wp-content/uploads /usr/src/wordpress/wp-content/uploads/wc-logs
RUN chmod -R 777 /usr/src/wordpress/wp-content/uploads
