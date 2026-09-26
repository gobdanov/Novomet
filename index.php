<?php
require __DIR__ . '/db.php';

$requests = db_query("SELECT
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
where executor_id = 4;

");
?>

<?php
// Функция возвращает CSS-класс цвета по приоритету и статусу
function card_color_class($priority, $status) {
    // Если заявка выполнена — всегда зелёная
    if ($status === 'done') {
        return 'div_for_color_border_green';
    }

    // Иначе смотрим на приоритет
    switch ($priority) {
        case 'critical': return 'div_for_color_border_red';
        case 'normal':   return 'div_for_color_border_yellow';
        case 'low':    return 'div_for_color_border_blue';
        default:          return 'div_for_color_border_green'; // на всякий случай
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
            <img src="images/novomet-logo.png" class="logo" alt="НОВОМЕТ"> 

            <div class="user_info">

                <div>
                    <img src="images/user-photo.png" class="user_photo" alt="ФОТОГРАФИЯ ПОЛЬЗОВАТЕЛЯ">
                </div>

                <div>
                    <p class="user_FIO">Богданов Вадим Александрович</p>
                    <p class="user_DOLZHNOST">электромонтёр</p>
                </div>
                    
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
                    <form class="filtration">
                        <label>
                            <input type="radio" name="color" value="red" checked>Все
                        </label>
                        <label>
                            <input type="radio" name="color" value="red">В работе
                        </label>
                    </form>

                    <p class="total_requests">Всего заявок: <?= count($requests) ?></p>
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
                                    <form>
                                        <label>
                                            <input class="button" type="button" value="Заполнить отчёт"
                                                   onclick="window.location.href='send_report.php?request_id=<?= $req['id'] ?>'">
                                        </label>
                                    </form>
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