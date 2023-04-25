FROM php:fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    supervisor \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Create system user to run Composer and Artisan Commands
RUN mkdir -p /home/$user
RUN useradd -G root -u $uid -d /home/$user $user
RUN chown -R $user:$user /home/$user

RUN mkdir -p /var/log/supervisor

CMD ["/usr/bin/supervisord"]
