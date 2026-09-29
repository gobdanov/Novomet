<?php
require __DIR__ . '/../config/db2.php';
require __DIR__ . '/../includes/functions.php';


$user = currentUser();

$requestId = (int)($_GET["request_id"] ?? 0);

if($requestId == null){
    http_response_code(400);
    die("не указан id записи");
}

$rows = db_query("
    SELECT
        r.id,
        w.name,
        r.well,
        r.well_cluster,
        rep.start_time,
        rep.end_time
    FROM requests r
        JOIN works w ON r.work_id = w.id
        LEFT JOIN reports rep ON rep.request = r.id
    WHERE r.id = ?
", [$requestId]);

if( empty($rows)){
    http_response_code(404);
    die("заявка не найдена");
}

$request = $rows[0];

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Нормализация дат
        $start = null;
if (!empty($_POST['start_time'])) {
    $start = str_replace('T', ' ', $_POST['start_time']);  // '2026-09-10 19:21'
    if (strlen($start) === 16) $start .= ':00';            // '2026-09-10 19:21:00'
    $start = str_replace(' ', 'T', $start);                // '2026-09-10T19:21:00'
}
if (!empty($_POST['end_time'])) {
    $end = str_replace('T', ' ', $_POST['end_time']);  // '2026-09-10 19:21'
    if (strlen($end) === 16) $end .= ':00';            // '2026-09-10 19:21:00'
    $end = str_replace(' ', 'T', $end);                // '2026-09-10T19:21:00'
}

        db_exec("
            INSERT INTO reports (
                executor_id,
                request,
                start_time,
                end_time,
                vvn,
                ped,
                i_n,
                u_n,
                load,
                r_iz,
                u_otp,
                p,
                t,
                zp,
                esp,
                descent_depth,
                nst,
                comment,
                photo
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $user["id"],
            $requestId,
            $start,
            $end,
            (float)($_POST['vvn']           ?? 0),
            (float)($_POST['ped']           ?? 0),
            (float)($_POST['i_n']           ?? 0),
            (float)($_POST['u_n']           ?? 0),
            (float)($_POST['load']          ?? 0),
            (float)($_POST['r_iz']          ?? 0),
            (float)($_POST['u_otp']         ?? 0),
            (float)($_POST['p']             ?? 0),
            (float)($_POST['t']             ?? 0),
            (float)($_POST['zp']            ?? 0),
            (float)($_POST['esp']           ?? 0),
            (float)($_POST['descent_depth'] ?? 0),
            (float)($_POST['nst']           ?? 0),
            $_POST['comment'] ?? null,
            $_POST['photo']   ?? null,
        ]);

        header('Location: index.php?saved=1');
        exit;

    } catch (Exception $e) {
        $error = 'Ошибка сохранения: ' . $e->getMessage();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style2.css">
    <title>Novomet2</title>
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
                <p class="user_FIO">Богданов Вадим Александрович</p>
                <p class="user_DOLZHNOST">электромонтёр</p>
            </div>
                
        </div>  
    </div>  
</header>   


    <div class="user_moves">

        <div class="user_moves_inner">

            <div class="upper_text">
                <div class="upper_text_left">
                    <h1 class="list_of_requests"><b>Заполнение отчёта</b></h1>
                    <p class="name_of_request"> №<?= htmlspecialchars($request["id"])?> <?= htmlspecialchars($request["name"])?> </p>
                </div>

                <div class="upper_text_right">
                    <p class="total_requests"> куст <?= htmlspecialchars($request["well"])?> • скважина <?= htmlspecialchars($request["well_cluster"])?> </p>
                </div>
            </div>
            
        </div>

        <hr>
        
        <form method="POST" action="">
            <div class="textboxes">
                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                <div class="row">
                    
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Время начала</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        <?php
                        $startVal = '';
                        if (!empty($request['start_time'])) {
                            $dt = $request['start_time'];
                            if ($dt instanceof DateTime) {
                                $startVal = $dt->format('Y-m-d\TH:i');
                            } else {
                                $startVal = date('Y-m-d\TH:i', strtotime($dt));
                            }
                        }
                        ?>
                        <input type="datetime-local" name="start_time" class="tb"
                            value="<?= htmlspecialchars($startVal) ?>" required>
                    </div>
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Время конца</p>
                            <p class="star_of_tb">*</p>
                        </div>
                        
                        
                        <input type="datetime-local" name="end_time" class="tb" required>
                    </div>
                </div>  

                <div class="row">
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">ВВН</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="vvn" step="any" required>
                    </div>
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">ПЭД</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="ped" step="any" required>
                    </div>
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Iн, А</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="i_n" step="any" required>
                    </div>
                </div>  

                <div class="row">
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Uн, В</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="u_n" step="any" required>
                    </div>
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Загрузка, %</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="load" step="any" required>
                    </div>
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Rиз, мОм</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="r_iz" step="any" required>
                    </div>
                </div>  

                <div class="row">
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Uотп, В</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="u_otp" step="any" required>
                    </div>
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">p, кВт</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="p" step="any" required>
                    </div>
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">T, °C </p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="t" step="any" required>
                    </div>
                </div>  
                
                <div class="row">
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">ЗП (забойное давление)</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="zp" step="any" required>
                    </div>
                    
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">ЭСП (статическое давление)</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="esp" step="any" required>
                    </div>
                </div>  

                <div class="row">
                    <div class="textbox">

                        <div class="textbox_div">
                            <p class="name_of_tb">Глубина спуска, м</p>
                            <p class="star_of_tb">*</p>
                        </div>

                        
                        <input type="number" class="tb" name="descent_depth" step="any" required>
                    </div>
                    <div class="textbox">
                        <div class="textbox_div">
                            <p class="name_of_tb">Нст (статич. уровень), м</p>
                            <p class="star_of_tb">*</p>
                        </div>
                        
                        <input type="number" class="tb" name="nst" step="any" required>
                    </div>
                </div>  

                <div class="row">
                    <div class="textbox">
                        <div class="textbox_div">
                            <p class="name_of_tb">Комментарий</p>
                        </div>  
                        <input type="text" name="comment" class="tb_var2">
                    </div>
                </div>
                
                <div class="row">
                    <div class="textbox">
                        <p class="name_of_tb">Фото</p>
                        <input type="text" name="photo" class="tb_var2">
                    </div>
                </div>
                

                <div class="warning_div">
                    <img src="img/warning_icon.png" class="warning_icon" alt="ВНИМАНИЕ">
                    <p class="warning">все поля с * обязательны к заполнению</p>
                </div>
                
            </div>

            <div class="buttons">
                <input type="submit"  class="button_left" value="Отправить отчёт" >
                <input type="button"  class="button_center" value="Сохранить в черновик" onclick="alert('Отчёт сохранён в черновик!')">
                <input type="button"  class="button_right" value="Отмена" onclick="window.location.href='index.html'">
            </div>
        </form>
    </div>
</body>

</html>