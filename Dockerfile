# Dockerfile
FROM php:8.2-fpm

# Install system deps + GD + common Laravel extensions
RUN apt-get update \
    && apt-get install -y \
        git \
        curl \
        wget \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libpq-dev \
        libmcrypt-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pdo_pgsql mbstring bcmath exif pcntl opcache \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js (for Vite / Tailwind)
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

# Copy composer files first (leverage cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-progress --prefer-dist

# Install PHP post‑install scripts (optional; usually done in app)
RUN composer run-script post-root-package-install --no-dev \
    && composer run-script post-autoload-dump --no-dev

# Copy app source
COPY . .

# Fix permissions and prepare Laravel
RUN usermod -u 1000 www-data \
    && chown -R www-www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && cp .env.example .env

# Build frontend assets (Vite + Tailwind + Flowbite)
RUN npm ci --no-progress \
    && npm run build --no-progress

# Generate Laravel config cache (production)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose PHP‑FPM port (Nginx will talk to 9000)
EXPOSE 9000

CMD ["php-fpm"]
