FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y nginx

# Copy Nginx configuration file
COPY ./docker-compose/nginx/crowdfunding-app.conf /etc/nginx/conf.d/default.conf

# Install additional PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add user
RUN if ! id -u $user > /dev/null 2>&1; then \
    useradd -G www-data,root -u $uid -d /home/$user $user; \
fi

RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Set permissions
RUN chown -R $user:www-data /var/www

# Switch to the new user
USER $user

# Install composer dependencies
RUN composer install --prefer-dist --no-scripts --no-dev --optimize-autoloader

# Expose port 80 for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "service nginx start && php-fpm"]
