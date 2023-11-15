FROM webdevops/php

RUN apt-get update -y && apt-get install -y --no-install-recommends \
        wget \
        curl \
    && rm -rf /var/lib/apt/lists/*

# Install PDO MYSQL
RUN #docker-php-ext-install pdo_mysql

WORKDIR /app

ADD --chown=application ./dbCreator.php ./
