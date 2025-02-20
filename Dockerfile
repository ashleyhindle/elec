FROM ashleyhindle/ssh-php:latest

# Switch to root

USER root

# You can add any additional PHP extensions by using `RUN apk add [extensions]`.

RUN apk add php-sqlite3

# --------------------------------------

# Copy all files

COPY .env .env
COPY ./whc.db ./whc.db
COPY ./src ./src
COPY ./composer.json ./

# Create a symbolic link

RUN ln -s ./src/index.php ./index.php

# Create the .ssh folder

RUN mkdir .ssh

# Update the server's ownership

RUN chown -R server:server /home/server

# Switch back to a lower privaledged user

USER server

# Run Composer install without dependencies

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
