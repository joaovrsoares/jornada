#!/bin/sh
set -e

export DB_HOST="${DB_HOST:-127.0.0.1}"
DB_DATABASE="${DB_DATABASE:-jornada}"
DB_USERNAME="${DB_USERNAME:-jornada}"
DB_PASSWORD="${DB_PASSWORD:-jornada_dev}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-root_dev}"

mkdir -p /var/log/supervisor /run/mysqld /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql

# Garante que o PHP-FPM consiga gravar os comprovantes enviados.
mkdir -p /var/www/storage/comprovantes
chown -R www-data:www-data /var/www/storage
chmod -R u+rwX,g+rwX /var/www/storage

# Inicializa o banco de dados se ainda não existir
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "==> Inicializando diretório de dados do MariaDB..."
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql --skip-test-db > /dev/null

    echo "==> Iniciando MariaDB temporário para configuração inicial..."
    mysqld_safe --user=mysql --datadir=/var/lib/mysql --skip-networking &
    pid="$!"

    for i in $(seq 30 -1 0); do
        if mariadb-admin ping --socket=/run/mysqld/mysqld.sock --silent; then
            break
        fi
        sleep 1
    done

    if [ "$i" = "0" ]; then
        echo "==> Erro: MariaDB temporário não iniciou."
        exit 1
    fi

    echo "==> Criando banco, usuários e permissões..."
    mariadb --socket=/run/mysqld/mysqld.sock <<-EOSQL
        ALTER USER 'root'@'localhost' IDENTIFIED BY '${DB_ROOT_PASSWORD}';
        CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;
        CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
        CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
        GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'%';
        GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'localhost';
        FLUSH PRIVILEGES;
EOSQL

    if [ -f /docker-entrypoint-initdb.d/init.sql ]; then
        echo "==> Executando init.sql..."
        mariadb --socket=/run/mysqld/mysqld.sock -u root -p"${DB_ROOT_PASSWORD}" "${DB_DATABASE}" < /docker-entrypoint-initdb.d/init.sql
    fi

    mariadb-admin --socket=/run/mysqld/mysqld.sock -u root -p"${DB_ROOT_PASSWORD}" shutdown
    wait "$pid"
    echo "==> Configuração inicial do MariaDB concluída com sucesso."
fi

exec "$@"
