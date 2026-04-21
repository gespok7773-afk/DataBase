FROM php:8.4-cli

RUN docker-php-ext-install pdo pdo_mysql mysqli

WORKDIR /app
COPY . .
CMD ["tail", "-f", "/dev/null"]