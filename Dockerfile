FROM php:8.2-apache

WORKDIR /var/www/html

# 1) Apache: rewrite + DocumentRoot 指向 public/
RUN a2enmod rewrite \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# 2) PHP extensions（Postgres 用 pdo_pgsql）
RUN apt-get update \
 && apt-get install -y git curl unzip nodejs npm libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*


# 3) Copy code
COPY . .

# 4) Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 5) Vite build（生成 public/build，不需要 npm run dev）
RUN npm ci && npm run build

# 6) Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod +x /var/www/html/start.sh

CMD ["/var/www/html/start.sh"]
