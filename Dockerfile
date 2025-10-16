# Use a specific PHP version with Apache
FROM php:8.2.12-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy app files to the web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Create a non-root user for security
RUN useradd -m appuser

# Make data folder writable and everything else read-only
RUN mkdir -p /var/www/html/data \
    && chown -R appuser:www-data /var/www/html/data \
    && chmod -R 770 /var/www/html/data \
    && find /var/www/html -type f -not -path "/var/www/html/data/*" -exec chmod 440 {} \; \
    && find /var/www/html -type d -not -path "/var/www/html/data/*" -exec chmod 550 {} \;

# Switch to non-root user
USER appuser

# Expose port 80
EXPOSE 80

# Use a read-only filesystem except for the writable data folder
VOLUME ["/var/www/html/data"]

# Switch back to root to start Apache securely
USER root

# Start Apache
CMD ["apache2-foreground"]
