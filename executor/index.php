<?php
require __DIR__ . '/../config/db2.php';
require __DIR__ . '/../includes/functions.php';

$user = currentUser();

// Фильтр
$filter  = $_GET['filter'] ?? 'all';
$allowed = ['all', 'work'];
if (!in_array($filter, $allowed, true)) $filter = 'all';

$sql = "SELECT
    r.id,
    u.username      AS dispatcher,
    o.name          AS operation,
    w.name          AS work,
    r.object_name,
    ss.name         AS subdivision,
    r.well,
    r.well_cluster,
    r.status,
    r.priority
FROM requests r
JOIN users u                 ON u.id  = r.dispatcher_id
JOIN operations o            ON o.id  = r.operation_id
JOIN works w                 ON w.id  = r.work_id
JOIN separate_subdivisions ss ON ss.id = r.separate_subdivision_id
WHERE r.executor_id = ?";

$params = [$user['id']];

if ($filter === 'work') {
    $sql .= " AND r.status = 'in_progress'";
}

$sql .= " ORDER BY r.id DESC";

$requests = db_query($sql, $params);

// Общее количество — для счётчика
$total = db_query(
    "SELECT COUNT(*) AS cnt FROM requests WHERE executor_id = ?",
    [$user['id']]
)[0]['cnt'] ?? 0;

// Функция возвращает CSS-класс цвета по приоритету и статусу
function card_color_class($priority, $status) {
    if ($status === 'done') {
        return 'div_for_color_border_green';
    }
    switch ($priority) {
        case 'critical': return 'div_for_color_border_red';
        case 'normal':   return 'div_for_color_border_yellow';
        case 'low':      return 'div_for_color_border_blue';
        default:         return 'div_for_color_border_green';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">
    <title>Novomet</title>
    
</head>

<body class="main_body">

    <header class="site_header">
        <div class="header_inner">
            <img src="img/novomet-logo.png" class="logo" alt="НОВОМЕТ"> 

            <div class="user_info">

                <div>
                    <img src="img/user-photo.png" class="user_photo" alt="ФОТОГРАФИЯ ПОЛЬЗОВАТЕЛЯ">
                </div>

                <div>
                    <p class="user_FIO"><b> <?=$user["surname"] ?> <?=$user["name"] ?> </b> </p>
                    <p class="user_DOLZHNOST"> <?=$user["role"] ?></p>
                </div>

                <a href="../public/logout.php" class="btn-outline-blue">Выйти</a>
                    
            </div>  
        </div>  
    </header>   


    <div class="user_moves">

        <div class="user_moves_inner">

            <div class="upper_text">
                <div class="line">
                    <div class="upper_text_left">
                        <h1 class="list_of_requests"><b>Список заявок</b></h1>

                        <div class="classes_of_requests">
                            <div class="item_class_of_request">
                                <div class="red_square_class"></div>
                                <p class="text_classes_of_request">Аварийный</p>
                            </div>
                            <div class="item_class_of_request">
                                <div class="yellow_square_class"></div>
                                <p class="text_classes_of_request">Средний</p>
                            </div>
                            <div class="item_class_of_request">
                                <div class="blue_square_class"></div>
                                <p class="text_classes_of_request">Низкий</p>
                            </div>
                            <div class="item_class_of_request">
                                <div class="green_square_class"></div>
                                <p class="text_classes_of_request">Выполнено</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="upper_text_right">
                    <form class="filtration" method="GET" action="">
                        <label>
                            <input type="radio" name="filter" value="all"
                                <?= $filter === 'all' ? 'checked' : '' ?>
                                onchange="this.form.submit()">
                            Все
                        </label>
                        <label>
                            <input type="radio" name="filter" value="work"
                                <?= $filter === 'work' ? 'checked' : '' ?>
                                onchange="this.form.submit()">
                            В работе
                        </label>
                    </form>

                    <?php
                    $total = db_query(
                        "SELECT COUNT(*) AS cnt FROM requests WHERE executor_id = ?",
                        [$user['id']]
                    )[0]['cnt'] ?? 0;
                    ?>
                    <p class="total_requests">
                        Всего заявок: <?= (int)$total ?>
                        <?php if ($filter === 'work'): ?>
                            (в работе: <?= count($requests) ?>)
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
        </div>

        <hr>

        <!-- элементы -->
        <div class="requests">
    <?php if (empty($requests)): ?>
        <p>Заявок нет</p>
    <?php else: ?>
        <?php foreach ($requests as $i => $req): ?>
            <div class="item_request">
                <div class="<?= card_color_class($req['priority'], $req['status']) ?>">
                    <div class="item_info">
                        <div class="upper_text_item">
                            <h1 class="number_of_request">№<?= htmlspecialchars($req['id']) ?></h1>
                            <p class="name_of_request"><?= htmlspecialchars($req['object_name'])?> <?= htmlspecialchars($req['work'])?> </p>
                        </div>
                    </div>
                    <table class="table_of_information">
                        <colgroup>
                            <col class="first_col_table">
                            <col class="second_col_table">
                            <col class="third_col_table">
                        </colgroup>
                        <tr>
                            <td>
                                <div class="info_in_table">
                                    <p class="name_of_table_info">Дата заявки:</p>
                                    <p class="value_of_table_info"><?= date('d.m.Y') ?></p>
                                </div>
                            </td>
                            <td colspan="2">
                                <div class="info_in_table">
                                    <p class="name_of_table_info">Куст:</p>
                                    <p class="value_of_table_info"> <?= htmlspecialchars($req['well']) ?> </p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info_in_table">
                                    <p class="name_of_table_info">Диспетчер:</p>
                                    <p class="value_of_table_info"><?= htmlspecialchars($req['dispatcher']) ?></p>
                                </div>
                            </td>
                            <td>
                                <div class="info_in_table">
                                    <p class="name_of_table_info">Скважина:</p>
                                    <p class="value_of_table_info"><?= htmlspecialchars($req['well_cluster']) ?></p>
                                </div>
                            </td>
                            <td>
                                <div class="info_in_table">
                                    <p class="name_of_table_info">Статус:</p>
                                    <p class="value_of_table_info"><?= htmlspecialchars($req['status']) ?></p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info_in_table">
                                    <?php if ($req['status'] == 'new'): ?>
                                        <form method="post" action="take_request.php">
                                            <label>
                                                <input type="hidden" name="requestId" value="<?= (int)$req['id'] ?>">
                                                <input class="button" type="submit" value="Взять в работу">
                                            </label>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($req['status'] == 'in_progress'): ?>
                                        <form>
                                            <label>
                                                <input class="button" type="button" value="Заполнить отчёт"
                                                    onclick="window.location.href='send_report.php?request_id=<?= $req['id'] ?>'">
                                            </label>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($req['status'] == 'done'): ?>
                                        <form>
                                            <label>
                                                <input class="button" type="button" value="Отчет отправлен">
                                            </label>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td colspan="2">
                                <a href='detailed_information.php?id=<?= $req['id'] ?>'>Подробнее</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

    </div>
</body>

</html>