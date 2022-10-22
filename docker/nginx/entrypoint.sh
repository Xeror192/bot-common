#!/usr/bin/env sh

envsubst "
\${COMPOSE_PROJECT_NAME}
\${APP_SITEMAP_DIR}" < "/etc/nginx/conf.d/default.conf.dist" > "/etc/nginx/conf.d/default.conf"

nginx -g 'daemon off;'
