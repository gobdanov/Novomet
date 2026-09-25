<?php
echo 'sqlsrv loaded: ';
var_dump(extension_loaded('sqlsrv'));
echo 'pdo_sqlsrv loaded: ';
var_dump(extension_loaded('pdo_sqlsrv'));
echo '<hr>';

$server = 'localhost';
$database = 'Novomet';
$user = 'user';
$password = 'NOVOMET';

$info = [
    'Database' => $database,
    'UID' => $user,
    'PWD' => $password,
    'TrustServerCertificate' => true,
    'CharacterSet' => 'UTF-8',
];

$conn = sqlsrv_connect($server, $info);

if($conn == false){
    echo '<b style = "color:red">Ошибка подключения:<b><br>';
    echo '<pre>';

    print_r(sqlsrv_errors());
    echo '</pre>';
    exit;
}

echo '<b style="color:green">Подключение успешно!</b><br><br>';

// Пробный запрос
$stmt = sqlsrv_query($conn, "SELECT COUNT(*) AS cnt FROM operations");
if ($stmt === false) {
    echo 'Ошибка запроса:<br><pre>';
    print_r(sqlsrv_errors());
    exit;
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
echo 'Операций в таблице operations: <b>' . $row['cnt'] . '</b>';

