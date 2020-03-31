FROM nginx:alpine

RUN \
    # add non-root user
    adduser -D nonroot nonroot \
    \
    # add nonroot to sudoers
    && apk add --update sudo \
    && echo "nonroot ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

RUN sed -i -E "s/error_log .+/error_log \/dev\/stderr debug;/" /etc/nginx/nginx.conf \
    && sed -i -E "s/access_log .+/access_log \/dev\/stdout main;/" /etc/nginx/nginx.conf \
    && sed -i -E "s/^user .*/user nonroot;/" /etc/nginx/nginx.conf

ENV NGINX_CONFD_DIR /etc/nginx/conf.d

COPY payments_api.conf /etc/nginx/conf.d/payments_api.conf

COPY start.sh /

RUN chmod a+x /start.sh

CMD ["/start.sh"]