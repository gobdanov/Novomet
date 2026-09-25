<?php
// db.php

$DB_SERVER   = 'localhost';   // или 'localhost'
$DB_NAME     = 'Novomet';
$DB_USER     = 'user';
$DB_PASSWORD = 'NOVOMET';

function db() {
    static $conn = null;
    if ($conn !== null) return $conn;

    global $DB_SERVER, $DB_NAME, $DB_USER, $DB_PASSWORD;

    $info = [
        'Database' => $DB_NAME,
        'UID'      => $DB_USER,
        'PWD'      => $DB_PASSWORD,
        'TrustServerCertificate' => true,
        'CharacterSet' => 'UTF-8',
    ];

    $conn = sqlsrv_connect($DB_SERVER, $info);

    if ($conn === false) {
        http_response_code(500);
        die('Ошибка подключения к БД: ' . print_r(sqlsrv_errors(), true));
    }

    return $conn;
}

function db_query($sql, $params = []) {
    $stmt = sqlsrv_query(db(), $sql, $params);
    if ($stmt === false) {
        http_response_code(500);
        die('Ошибка запроса: ' . print_r(sqlsrv_errors(), true));
    }

    $rows = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $rows[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    return $rows;
}

function db_exec($sql, $params = []) {
    $stmt = sqlsrv_query(db(), $sql, $params);
    if ($stmt === false) {
        http_response_code(500);
        die('Ошибка выполнения: ' . print_r(sqlsrv_errors(), true));
    }
    sqlsrv_free_stmt($stmt);
    return true;
}