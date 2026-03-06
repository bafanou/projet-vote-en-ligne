FROM php:8.1-apache

# Copier tous les fichiers du projet dans Apache
COPY . /var/www/html/

# Donner les bonnes permissions
RUN chmod -R 755 /var/www/html/

# Exposer le port 80
EXPOSE 80
