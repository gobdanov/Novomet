<?php
require __DIR__ . '/db.php';

$requestId = (int)($_GET["request_id"] ?? 0);

if($requestId == null){
    http_response_code(400);
    die("не указан id записи");
}

$rows = db_query("
    select r.id, w.name, r.well, r.well_cluster from [requests] r
join works w on r.work_id = w.id where r.id = ?
", [$requestId]);

if( empty($rows)){
    http_response_code(404);
    die("заявка не найдена");
}

$request = $rows[0];
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
                <div class="upper_text_left">
                    <h1 class="list_of_requests"><b>Заполнение отчёта</b></h1>
                    <p class="name_of_request"> №<?= htmlspecialchars($request["id"])?> <?= htmlspecialchars($request["name"])?> </p>
                </div>

                <div class="upper_text_right">
                    <p class="total_requests"><?= htmlspecialchars($request["well"])?> • <?= htmlspecialchars($request["well_cluster"])?> </p>
                </div>
            </div>
            
        </div>

        <hr>
        
        <div class="textboxes">
            <div class="row">
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Время начала</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Время конца</p>
                        <p class="star_of_tb">*</p>
                    </div>
                    
                    
                    <input type="text" class="tb" >
                </div>
            </div>  

            <div class="row">
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">ВВН</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb" >
                </div>
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">ПЭД</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb" >
                </div>
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Iн, А</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
            </div>  

            <div class="row">
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Uн, В</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Загрузка, %</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Rиз, мОм</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
            </div>  

            <div class="row">
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Uотп, В</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">p, кВт</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">T, °C </p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
            </div>  
            
            <div class="row">
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">ЗП (забойное давление)</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
                
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">ЭСП (статическое давление)</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
            </div>  

            <div class="row">
                <div class="textbox">

                    <div class="textbox_div">
                        <p class="name_of_tb">Глубина спуска, м</p>
                        <p class="star_of_tb">*</p>
                    </div>

                    
                    <input type="text" class="tb">
                </div>
                <div class="textbox">
                    <div class="textbox_div">
                        <p class="name_of_tb">Нст (статич. уровень), м</p>
                        <p class="star_of_tb">*</p>
                    </div>
                    
                    <input type="text" class="tb">
                </div>
            </div>  

            <div class="row">
                <div class="textbox">
                    <div class="textbox_div">
                        <p class="name_of_tb">Комментарий</p>
                    </div>  
                    <input type="text" class="tb_var2">
                </div>
            </div>
            
            <div class="row">
                <div class="textbox">
                    <p class="name_of_tb">Фото</p>
                    <input type="text" class="tb_var2">
                </div>
            </div>
            

            <div class="warning_div">
                <img src="images/warning_icon.png" class="warning_icon" alt="ВНИМАНИЕ">
                <p class="warning">все поля с * обязательны к заполнению</p>
            </div>
            
        </div>

        <div class="buttons">
            <input type="button"  class="button_left" value="Отправить отчёт" onclick="alert('Отчёт отправлен!')">
            <input type="button"  class="button_center" value="Сохранить в черновик" onclick="alert('Отчёт сохранён в черновик!')">
            <input type="button"  class="button_right" value="Отмена" onclick="window.location.href='index.html'">
            
        </div>

    </div>
</body>

</html>