<?php

createDatabase(
    getenv('DB_HOST'),
    getenv('DB_ROOTUSER'),
    getenv('DB_ROOTPWD'),
    getenv('DB_USER'),
    getenv('DB_PWD'),
    getenv('DB_NAME'),
    getenv('MAX_QUERY_PER_HOUR'),
    getenv('MAX_CONNECTIONS_PER_HOUR'),
    getenv('MAX_UPDATES_PER_HOUR'),
    getenv('MAX_USER_CONNECTIONS')
);

function createDatabase(
    $host,
    $rootUser,
    $rootPassword,
    $user,
    $password,
    $databaseName,
    $maxQueryPerHour = 0,
    $maxConnectionsPerHour = 0,
    $maxUpdatesPerHour = 0,
    $maxUserConnections = 0
)
{
    try {
        $dbConnection = new mysqli($host, $rootUser, $rootPassword);

        if ($dbConnection->connect_error) {
            throw new Exception('Connect Error (' . $dbConnection->connect_errno . ') '
                . $dbConnection->connect_error, 500);
        }

        $query = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '$user')";
        /** @var \mysqli_result $result */
        $result = $dbConnection->query($query);
        if ($dbConnection->errno > 0) {
            throw new Exception('Query Error (' . $dbConnection->errno . ') '
                . $dbConnection->error, 500);
        }
        $result = $result->fetch_array();
        if ((int)$result[0] === 0) {
            $query = "CREATE USER `$user`@`%`;";
            $result = $dbConnection->query($query);
            if ($result !== TRUE) {
                throw new Exception(sprintf("Create User Error (' %s ')", $dbConnection->error), 500);
            }
            echo "[OK] USER $user created!\n";
        }
        $query = "SET PASSWORD FOR `$user`@`%` = PASSWORD('$password');";
        $result = $dbConnection->query($query);
        if ($result !== TRUE) {
            throw new Exception(sprintf("Set User Password Error (' %s ')", $dbConnection->error), 500);
        }
        echo "[OK] Password for USER $user updated!\n";
        $query = "GRANT USAGE ON *.* TO '$user'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR " . $maxQueryPerHour . " MAX_CONNECTIONS_PER_HOUR " . $maxConnectionsPerHour . " MAX_UPDATES_PER_HOUR " . $maxUpdatesPerHour . " MAX_USER_CONNECTIONS " . $maxUserConnections . ";";
        $result = $dbConnection->query($query);
        if ($result !== TRUE) {
            throw new Exception(sprintf("Grant User Usage Permissions Error (' %s ')", $dbConnection->error), 500);
        }
        $query = "CREATE DATABASE IF NOT EXISTS `$databaseName` CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
        $result = $dbConnection->query($query);
        if ($result !== TRUE) {
            throw new Exception(sprintf("Create Database Error (' %s ')", $dbConnection->error), 500);
        }
        echo "[OK] DB $databaseName created if not already exists!\n";
        $query = "GRANT ALL PRIVILEGES ON `$databaseName`.* TO '$user'@'%';";
        $result = $dbConnection->query($query);
        if ($result !== TRUE) {
            throw new Exception(sprintf("Grant All Privileges Database Error (' %s ')", $dbConnection->error), 500);
        }
        echo "[OK] Job finished\n";
    } catch (Exception $exception) {
        echo "[ERR] " . $exception->getMessage() . "\n";
        echo "[ERR] " . $exception->getLine() . "\n";
    }
}

