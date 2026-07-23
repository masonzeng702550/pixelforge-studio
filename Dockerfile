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

# Runtime data dir (env-exposed) holds boot/cache scratch — including a DECOY flag
# that an attacker reaches with a plain `ls` after RCE. The REAL secret lives in the
# vault dir, whose path is only in lib/config.php (must read app source to learn it).
# FLAG is passed at build time and never stored in source control; filename is random.
ENV PIXELFORGE_DATA=/opt/pixelforge/.runtime
ARG FLAG="THJCC{placeholder_pass_FLAG_build_arg}"
RUN mkdir -p "$PIXELFORGE_DATA" /var/lib/pixelforge/vault \
 && printf '%s\n' 'THJCC{b00t_c4ch3_1s_n0t_th3_fl4g}' > "$PIXELFORGE_DATA/THJCC_boot.flag" \
 && RID="$(head -c5 /dev/urandom | od -An -tx1 | tr -d ' \n')" \
 && printf '%s\n' "$FLAG" > "/var/lib/pixelforge/vault/vault_${RID}.flag" \
 && chmod 0755 "$PIXELFORGE_DATA" /var/lib/pixelforge /var/lib/pixelforge/vault \
 && chmod 0644 "$PIXELFORGE_DATA/THJCC_boot.flag" "/var/lib/pixelforge/vault/vault_${RID}.flag"

# Human Shield + staging-banner defaults (override via compose/env).
ENV CAPTCHA_MODE=arith \
    CAPTCHA_TTL=90 \
    CAPTCHA_ON_FIRE=true \
    INJECTION_LEVEL=2

EXPOSE 80
