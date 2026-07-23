FROM php:8.2-apache

# GD is used to render the Human Shield challenge image.
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpng-dev \
 && docker-php-ext-install gd \
 && apt-get purge -y --auto-remove \
 && rm -rf /var/lib/apt/lists/*

# Application source served by Apache.
COPY src/ /var/www/html/

# Writable dirs for the render cache and PHP sessions.
RUN mkdir -p /var/www/html/storage/cache \
 && chown -R www-data:www-data /var/www/html/storage \
 && chmod 0775 /var/www/html/storage/cache \
 && mkdir -p /tmp/php_sessions \
 && chown www-data:www-data /tmp/php_sessions \
 && chmod 0700 /tmp/php_sessions

# Runtime data directory + secret token. The value is passed at build time via
# the FLAG build-arg and is NOT stored in source control; the filename is random.
ENV PIXELFORGE_FLAG_DIR=/opt/pixelforge/.runtime
ARG FLAG="THJCC{placeholder_pass_FLAG_build_arg}"
RUN mkdir -p "$PIXELFORGE_FLAG_DIR" \
 && RID="$(head -c4 /dev/urandom | od -An -tx1 | tr -d ' \n')" \
 && printf '%s\n' "$FLAG" > "$PIXELFORGE_FLAG_DIR/THJCC_${RID}.flag" \
 && chmod 0755 "$PIXELFORGE_FLAG_DIR" \
 && chmod 0644 "$PIXELFORGE_FLAG_DIR/THJCC_${RID}.flag"

# Human Shield + staging-banner defaults (override via compose/env).
ENV CAPTCHA_MODE=arith \
    CAPTCHA_TTL=90 \
    CAPTCHA_ON_FIRE=true \
    INJECTION_LEVEL=2

EXPOSE 80
