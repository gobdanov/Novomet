<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'NOVOMET2');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'sqlsrv:Server=%s;Database=%s;TrustServerCertificate=1;Encrypt=1',
            DB_HOST,
            DB_NAME
        );

        try {
            $pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB connection error: ' . $e->getMessage());
            http_response_code(500);
            exit('Ошибка подключения к базе данных: ' . $e->getMessage());
        }
    }
    return $pdo;
}