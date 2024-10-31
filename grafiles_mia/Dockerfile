# Dockerfile para el Frontend en PHP

# Usa una imagen base de PHP 8.3 con Apache
FROM php:8.3-apache

# Copia el código del proyecto PHP al directorio de Apache
#COPY ./frontend /var/www/html/

# Instala extensiones adicionales si necesitas conectar PHP con MongoDB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Configura la carpeta de trabajo
#WORKDIR /var/www/html



# Asegúrate de que los permisos estén configurados correctamente
RUN chown -R www-data:www-data /var/www/html

# Expone el puerto 80 para Apache
EXPOSE 80
