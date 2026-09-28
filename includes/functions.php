<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Экранирование HTML */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Текущий пользователь */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

/** Требует авторизации, иначе редирект на login.php */
function requireAuth(): void
{
    if (!currentUser()) {
        // Абсолютный путь от корня сайта — работает из любой подпапки
        $loginUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        // Если мы уже в public/ — оставляем как есть, иначе поднимаемся на уровень
        if (basename($loginUrl) !== 'public' && is_dir(__DIR__ . '/../public')) {
            $loginUrl = '/login.php';
        } else {
            $loginUrl = '/login.php';
        }
        header('Location: ' . $loginUrl);
        exit;
    }
}

/**
 * Получить справочники для формы создания заявки
 * (operations, works, commodities, customers, productions,
 *  processings, separate_subdivisions, posts, resource_groups, users-исполнители)
 */
function getFormDictionaries(PDO $pdo): array
{
    $sql = [
        'operations'            => "SELECT id, name FROM operations ORDER BY name",
        'works'                 => "SELECT id, name FROM works ORDER BY name",
        'commodities'           => "SELECT id, name FROM commodities ORDER BY name",
        'customers'             => "SELECT id, name FROM customers ORDER BY name",
        'productions'           => "SELECT id, name FROM productions ORDER BY name",
        'processings'           => "SELECT id, name FROM processings ORDER BY name",
        'separate_subdivisions' => "SELECT id, name FROM separate_subdivisions ORDER BY name",
        'posts'                 => "SELECT id, name FROM posts ORDER BY name",
        'resource_groups'       => "SELECT id, name FROM resource_groups ORDER BY name",
        'executors'             => "SELECT id, surname, name FROM users WHERE role = N'executor' ORDER BY surname",
    ];

    $result = [];
    foreach ($sql as $key => $query) {
        $result[$key] = $pdo->query($query)->fetchAll();
    }
    return $result;
}