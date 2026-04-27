FROM php:8.4-cli

RUN docker-php-ext-install pdo pdo_mysql mysqli

WORKDIR /app
COPY . .
CMD ["php", "-S", "0.0.0.0:80", "-t", "/app"]