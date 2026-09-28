<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$pdo  = getDB();
$user = currentUser();

// Фильтр
$filter  = $_GET['filter'] ?? 'all';
$allowed = ['all','new','work','done'];
if (!in_array($filter, $allowed, true)) $filter = 'all';

$sql = "SELECT RequestID, DispatcherName, ExecutorName, OperationName,
               ObjectName, CustomerName, ActualStartTime, ActualEndTime,
               DurationMinutes, [Status], MasterActionRequired
        FROM vw_MasterDashboard";

if ($filter === 'done')      $sql .= " WHERE [Status] = N'Выполнено'";
elseif ($filter === 'work')  $sql .= " WHERE [Status] = N'В работе'";
elseif ($filter === 'new')   $sql .= " WHERE [Status] = N'Новая / В пути'";
$sql .= " ORDER BY RequestID DESC";

$requests = $pdo->query($sql)->fetchAll();

// Статистика
$stats = $pdo->query(
    "SELECT
        SUM(CASE WHEN [Status] = N'Новая / В пути' THEN 1 ELSE 0 END) AS new_count,
        SUM(CASE WHEN [Status] = N'В работе'       THEN 1 ELSE 0 END) AS work_count,
        SUM(CASE WHEN [Status] = N'Выполнено'      THEN 1 ELSE 0 END) AS done_count,
        COUNT(*) AS total
     FROM vw_MasterDashboard"
)->fetch();

// Приоритет в БД пока нет — поставим заглушку
function priorityBadge(array $r): string {
    // Здесь можно смотреть на что-то в данных, пока просто "средний"
    return '<span class="badge bg-yellow">СРЕДНИЙ</span>';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>НОВОМЕТ - Список заявок</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <div class="container header-inner">
        <div class="logo">
            <img src="img/logo.png" alt="НОВОМЕТ" class="logo-img">
        </div>
        <div class="header-right">
            <a href="create.php" class="btn-outline-blue">Добавить заявку</a>
            <div class="user-profile">
                <div class="avatar"><?= e(mb_substr($user['name'], 0, 1)) ?></div>
                <div class="user-info">
                    <div class="user-name"><?= e($user['surname'].' '.$user['name']) ?></div>
                    <div class="user-role"><?= e($user['role']) ?></div>
                </div>
            </div>
            <a href="../public/logout.php" class="btn-outline-blue">Выйти</a>
        </div>
    </div>
</header>

<main class="container">
    <div class="stats-grid">
        <div class="stat-card border-blue">
            <div class="stat-title">НОВЫЕ</div>
            <div class="stat-value blue"><?= (int)$stats['new_count'] ?> <span>заявок</span></div>
        </div>
        <div class="stat-card border-yellow">
            <div class="stat-title">В РАБОТЕ</div>
            <div class="stat-value yellow"><?= (int)$stats['work_count'] ?> <span>заявок</span></div>
        </div>
        <div class="stat-card border-green">
            <div class="stat-title">ВЫПОЛНЕНО</div>
            <div class="stat-value green"><?= (int)$stats['done_count'] ?> <span>заявок</span></div>
        </div>
        <div class="stat-card border-red">
            <div class="stat-title">ВСЕГО</div>
            <div class="stat-value red"><?= (int)$stats['total'] ?> <span>заявок</span></div>
        </div>
    </div>

    <div class="content-card">
        <div class="table-header-controls">
            <h2>Список заявок <span class="count">всего: <?= count($requests) ?></span></h2>
            <form method="GET" class="filters">
                <label class="radio-label"><input type="radio" name="filter" value="all"
                    <?= $filter==='all'?'checked':'' ?> onchange="this.form.submit()"> все</label>
                <label class="radio-label"><input type="radio" name="filter" value="new"
                    <?= $filter==='new'?'checked':'' ?> onchange="this.form.submit()"> новая</label>
                <label class="radio-label"><input type="radio" name="filter" value="work"
                    <?= $filter==='work'?'checked':'' ?> onchange="this.form.submit()"> в работе</label>
                <label class="radio-label"><input type="radio" name="filter" value="done"
                    <?= $filter==='done'?'checked':'' ?> onchange="this.form.submit()"> выполнено</label>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>№</th>
                    <th>ОПЕРАЦИЯ</th>
                    <th>ОБЪЕКТ</th>
                    <th>ИСПОЛНИТЕЛЬ</th>
                    <th>СТАТУС</th>
                    <th>ДЕЙСТВИЕ</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$requests): ?>
                <tr><td colspan="6">Заявок не найдено</td></tr>
            <?php else: foreach ($requests as $r): ?>
                <tr>
                    <td><?= (int)$r['RequestID'] ?></td>
                    <td><?= e($r['OperationName']) ?></td>
                    <td><?= e($r['ObjectName']) ?></td>
                    <td><?= e($r['ExecutorName'] ?? '—') ?></td>
                    <td><?= e($r['Status']) ?></td>
                    <td><button class="btn-action btn-control">Контроль</button></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>