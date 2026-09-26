<?php
require __DIR__ . '/db.php';

// Принимаем id из URL
$requestId = (int)($_GET["id"] ?? 0);

if ($requestId === 0) {
    http_response_code(400);
    die("Не указан ID заявки");
}

// Забираем заявку со ВСЕМИ полями requests + связанные данные
$rows = db_query("
SELECT
    -- сама заявка (все колонки requests)
    r.[id]                       AS req_id,
    r.[dispatcher_id]            AS req_dispatcher_id,
    r.[operation_id]             AS req_operation_id,
    r.[work_id]                  AS req_work_id,
    r.[commodity_id]             AS req_commodity_id,
    r.[production_id]            AS req_production_id,
    r.[customer_id]              AS req_customer_id,
    r.[object_name]              AS req_object_name,
    r.[processing_id]            AS req_processing_id,
    r.[separate_subdivision_id]  AS req_separate_subdivision_id,
    r.[post_id]                  AS req_post_id,
    r.[resource_group_id]        AS req_resource_group_id,

    -- диспетчер
    u.[id]                       AS d_id,
    u.[name]                     AS d_name,
    u.[surname]                  AS d_surname,
    u.[lastname]                 AS d_lastname,
    u.[username]                 AS d_username,
    u.[role]                     AS d_role,

    -- операция
    op.[id]                      AS op_id,
    op.[code]                    AS op_code,
    op.[name]                    AS op_name,

    -- работа
    wrk.[id]                     AS wrk_id,
    wrk.[code]                   AS wrk_code,
    wrk.[name]                   AS wrk_name,

    -- товарная группа
    c.[id]                       AS com_id,
    c.[code]                     AS com_code,
    c.[name]                     AS com_name,

    -- производство
    pr.[id]                      AS prod_id,
    pr.[code]                    AS prod_code,
    pr.[name]                    AS prod_name,

    -- клиент
    cu.[id]                      AS cust_id,
    cu.[code]                    AS cust_code,
    cu.[name]                    AS cust_name,

    -- вид обработки
    prc.[id]                     AS proc_id,
    prc.[code]                   AS proc_code,
    prc.[name]                   AS proc_name,

    -- обособленное подразделение
    ss.[id]                      AS sub_id,
    ss.[code]                    AS sub_code,
    ss.[name]                    AS sub_name,

    -- должность
    p.[id]                       AS post_id,
    p.[code]                     AS post_code,
    p.[name]                     AS post_name,

    -- группа ресурсов
    rg.[id]                      AS rg_id,
    rg.[code]                    AS rg_code,
    rg.[name]                    AS rg_name

FROM [requests] r
    JOIN [users]                 u   ON r.[dispatcher_id]           = u.[id]
    JOIN [operations]            op  ON r.[operation_id]            = op.[id]
    JOIN [works]                 wrk ON r.[work_id]                 = wrk.[id]
    JOIN [commodities]           c   ON r.[commodity_id]            = c.[id]
    JOIN [productions]           pr  ON r.[production_id]           = pr.[id]
    JOIN [customers]             cu  ON r.[customer_id]             = cu.[id]
    JOIN [processings]           prc ON r.[processing_id]           = prc.[id]
    JOIN [separate_subdivisions] ss  ON r.[separate_subdivision_id] = ss.[id]
    JOIN [posts]                 p   ON r.[post_id]                 = p.[id]
    JOIN [resource_groups]       rg  ON r.[resource_group_id]       = rg.[id]
    WHERE r.[id] = ?
", [$requestId]);

if (empty($rows)) {
    http_response_code(404);
    die("Заявка не найдена");
}

$request = $rows[0];

// ФИО диспетчера
$dispatcherFio = trim(
    ($request['d_surname'] ?? '') . ' ' .
    ($request['d_name']    ?? '') . ' ' .
    ($request['d_lastname']?? '')
);

// Хелпер: пустое значение → тире
function v($value) {
    return ($value === null || $value === '') ? '—' : htmlspecialchars($value);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Заявка №<?= $request['req_id'] ?></title>
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
                        <h1 class="list_of_requests"><b>Подробная информация о заявке № <?= v($request['req_id']) ?></b></h1>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="requests">

            <table class="table_of_information" style="width: 100%; padding-top: 20px;">
                <colgroup>
                    <col style="width: 40%;">
                    <col style="width: 60%;">
                </colgroup>

                
                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">ID заявки:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['req_id']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Объект:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['req_object_name']) ?></p></div></td>
                </tr>

                <!-- ==== ДИСПЕТЧЕР ==== -->
                 

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">ФИО диспетчера:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($dispatcherFio) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Логин диспетчера:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['d_username']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Роль диспетчера:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['d_role']) ?></p></div></td>
                </tr>

                <!-- ==== ОПЕРАЦИЯ ==== -->

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код операции:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['op_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Операция:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['op_name']) ?></p></div></td>
                </tr>

                <!-- ==== РАБОТА ==== -->

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код работы:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['wrk_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Работа:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['wrk_name']) ?></p></div></td>
                </tr>

                <!-- ==== ТОВАРНАЯ ГРУППА ==== -->
                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код товарной группы:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['com_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Товарная группа:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['com_name']) ?></p></div></td>
                </tr>

                <!-- ==== ПРОИЗВОДСТВО ==== -->

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код производства:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['prod_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Производство:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['prod_name']) ?></p></div></td>
                </tr>

                <!-- ==== КЛИЕНТ ==== -->

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код клиента:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['cust_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Клиент:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['cust_name']) ?></p></div></td>
                </tr>

                <!-- ==== ВИД ОБРАБОТКИ ==== -->
                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код вида обработки:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['proc_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Вид обработки:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['proc_name']) ?></p></div></td>
                </tr>

                <!-- ==== ОБОСОБЛЕННОЕ ПОДРАЗДЕЛЕНИЕ ==== -->

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код подразделения:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['sub_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Обособленное подразделение:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['sub_name']) ?></p></div></td>
                </tr>

                <!-- ==== ДОЛЖНОСТЬ ==== -->

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код должности:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['post_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Должность:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['post_name']) ?></p></div></td>
                </tr>

                <!-- ==== ГРУППА РЕСУРСОВ ==== -->

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Код группы ресурсов:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['rg_code']) ?></p></div></td>
                </tr>

                <tr>
                    <td><div class="info_in_table"><p class="name_of_table_info">Группа ресурсов:</p></div></td>
                    <td><div class="info_in_table"><p class="value_of_table_info"><?= v($request['rg_name']) ?></p></div></td>
                </tr>

            </table>

        </div>

    </div>

</body>
</html>