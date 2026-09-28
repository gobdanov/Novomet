<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = getDB();
    echo "Подключение OK<br>";
    echo "Пользователь: " . $pdo->query("SELECT SUSER_SNAME()")->fetchColumn() . "<br>";
    echo "База: " . $pdo->query("SELECT DB_NAME()")->fetchColumn() . "<br>";

    $tables = $pdo->query("SELECT name FROM sys.tables ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    echo "Таблиц: " . count($tables) . "<br>";
    echo implode(', ', $tables);
} catch (Throwable $e) {
    echo "Ошибка" . $e->getMessage();
}