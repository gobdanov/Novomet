<?php
require __DIR__ . '/../config/db2.php';
require __DIR__ . '/../includes/functions.php';

$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$requestId = (int)($_POST['requestId'] ?? 0);
if ($requestId <= 0) {
    header('Location: index.php');
    exit;
}

db_exec("UPDATE requests
           SET status = 'in_progress'
         WHERE id = ?
           AND executor_id = ?
           AND status = 'new'", [$requestId, $user['id']]);

header('Location: index.php');
exit;