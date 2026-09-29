<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$pdo     = getDB();
$user    = currentUser();
$error   = '';
$success = '';
$dict    = getFormDictionaries($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['operation_id','work_id','object_name','well','well_cluster','commodity_id',
                 'customer_id','resource_group_id','production_id','processing_id',
                 'separate_subdivision_id','post_id','executor_id','priority'];
    foreach ($required as $f) {
        if (empty($_POST[$f])) { $error = 'Заполните все обязательные поля'; break; }
    }

    // Приводим priority к значениям CHECK-констрейнта: critical / normal / low
    $priorityMap = [
        'high'   => 'critical',
        'medium' => 'normal',
        'low'    => 'low',
    ];
    $priority = $priorityMap[$_POST['priority'] ?? ''] ?? 'normal';

    if (!$error) {
        try {
            $stmt = $pdo->prepare(
                "EXEC dbo.sp_AssignMultipleRequests
                    @DispatcherID          = :dispatcher,
                    @ExecutorID            = :executor,
                    @ObjectName            = :object,
                    @OperationID           = :operation,
                    @WorkID                = :work,
                    @CommodityID           = :commodity,
                    @ProductionID          = :production,
                    @CustomerID            = :customer,
                    @ProcessingID          = :processing,
                    @SeparateSubdivisionID = :subdivision,
                    @PostID                = :post,
                    @ResourceGroupID       = :resource,
                    @Priority              = :priority,
                    @Well                  = :well,
                    @WellCluster           = :cluster"
            );
            $stmt->execute([
                ':dispatcher'  => $user['id'],
                ':executor'    => (int)$_POST['executor_id'],
                ':object'      => $_POST['object_name'],
                ':operation'   => (int)$_POST['operation_id'],
                ':work'        => (int)$_POST['work_id'],
                ':commodity'   => (int)$_POST['commodity_id'],
                ':production'  => (int)$_POST['production_id'],
                ':customer'    => (int)$_POST['customer_id'],
                ':processing'  => (int)$_POST['processing_id'],
                ':subdivision' => (int)$_POST['separate_subdivision_id'],
                ':post'        => (int)$_POST['post_id'],
                ':resource'    => (int)$_POST['resource_group_id'],
                ':priority'    => $priority,
                ':well'        => $_POST['well'],
                ':cluster'     => $_POST['well_cluster'],
            ]);
            $success = 'Заявка создана и передана исполнителю.';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Ошибка создания: ' . $e->getMessage();
        }
    }
}

/** Хелпер для вывода select-а */
function renderSelect(string $name, array $items, string $placeholder, string $label, string $req = '*'): void {
    echo '<div class="form-group half">';
    echo '<label for="'.e($name).'">'.e($label).' <span class="req">'.e($req).'</span></label>';
    echo '<div class="select-wrapper">';
    echo '<select id="'.e($name).'" name="'.e($name).'" required>';
    echo '<option value="" disabled selected>'.e($placeholder).'</option>';
    foreach ($items as $it) {
        echo '<option value="'.(int)$it['id'].'">'.e($it['name']).'</option>';
    }
    echo '</select></div></div>';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>НОВОМЕТ — Создать заявку</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <div class="container header-inner">
        <div class="logo"><img src="img/logo.png" alt="НОВОМЕТ" class="logo-img"></div>
        <div class="header-right">
            <div class="user-profile">
                <div class="avatar"><?= e(mb_substr($user['name'],0,1)) ?></div>
                <div>
                    <div class="user-name"><?= e($user['surname'].' '.$user['name'].' '.$user['lastname']) ?></div>
                    <div class="user-role"><?= e($user['role']) ?></div>
                </div>
            </div>
            <a href="index.php" class="btn-outline-blue">К списку</a>
        </div>
    </div>
</header>

<main class="container">
    <div class="form-card">
        <h1 class="form-title">Создать заявку</h1>

        <?php if ($error): ?>
            <div class="warning-text" style="margin-bottom:16px;">
                <span class="icon-warn">⚠</span> <?= e($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="color:#44AA00; margin-bottom:16px;">
                ✓ <?= e($success) ?> <a href="index.php">К списку →</a>
            </div>
        <?php endif; ?>

        <form action="create.php" method="POST">

            <div class="form-row">
                <?php renderSelect('operation_id', $dict['operations'], 'Выберите тип операции', 'Тип операции'); ?>
                <?php renderSelect('work_id',      $dict['works'],      'Выберите вид работы',  'Вид работы'); ?>
            </div>

            <!-- ==== ОБЪЕКТ / КУСТ / СКВАЖИНА ==== -->

            <div class="form-row">
                <div class="form-group half">
                    <label for="object_name">Объект <span class="req">*</span></label>
                    <input type="text" id="object_name" name="object_name"
                           placeholder="Например: АО НЕФТЕДОБЫЧА, Г.ПЕРМЬ" required>
                </div>
                <div class="form-group half">
                    <label for="well_cluster">Куст <span class="req">*</span></label>
                    <input type="text" id="well_cluster" name="well_cluster"
                           placeholder="Например: 12" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="well">Скважина <span class="req">*</span></label>
                    <input type="text" id="well" name="well"
                           placeholder="Например: 1024" required>
                </div>
                <?php renderSelect('commodity_id', $dict['commodities'], 'Выберите груз/ТМЦ', 'Груз / ТМЦ'); ?>
            </div>

            <!-- ==== ОСТАЛЬНОЕ ==== -->

            <div class="form-row">
                <?php renderSelect('customer_id',       $dict['customers'],             'Выберите заказчика',      'Заказчик'); ?>
                <?php renderSelect('resource_group_id', $dict['resource_groups'],       'Выберите группу ресурсов','Группа ресурсов'); ?>
            </div>

            <div class="form-row">
                <?php renderSelect('production_id', $dict['productions'], 'Выберите производство', 'Производство'); ?>
                <?php renderSelect('processing_id', $dict['processings'], 'Выберите техпроцесс',   'Техпроцесс'); ?>
            </div>

            <div class="form-row">
                <?php renderSelect('separate_subdivision_id', $dict['separate_subdivisions'], 'Выберите подразделение', 'Обособленное подразделение'); ?>
                <?php renderSelect('post_id',                 $dict['posts'],                 'Выберите пост',          'Пост / Объект'); ?>
            </div>

            <div class="form-row">
                <div class="form-group half">
                    <label for="priority">Приоритет <span class="req">*</span></label>
                    <div class="priority-select">
                        <span class="color-dot red-dot" id="priority-indicator"></span>
                        <select id="priority" name="priority" required onchange="updatePriorityColor(this)">
                            <option value="high"   data-color="red-dot">Аварийный</option>
                            <option value="medium" data-color="yellow-dot" selected>Средний</option>
                            <option value="low"    data-color="blue-dot">Низкий</option>
                        </select>
                    </div>
                </div>
                <div class="form-group half">
                    <label for="executor_id">Исполнитель <span class="req">*</span></label>
                    <div class="select-wrapper">
                        <select id="executor_id" name="executor_id" required>
                            <option value="" disabled selected>Выберите исполнителя</option>
                            <?php foreach ($dict['executors'] as $u): ?>
                                <option value="<?= (int)$u['id'] ?>">
                                    <?= e($u['surname'].' '.$u['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="comment">Комментарий</label>
                <textarea id="comment" name="comment" placeholder="Дополнительная информация..."></textarea>
            </div>

            <div class="warning-text">
                <span class="icon-warn">⚠</span> Все поля со * обязательны к заполнению!
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Отправить заявку</button>
                <a href="index.php" class="btn-cancel"
                   style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">Отмена</a>
            </div>
        </form>
    </div>
</main>

<script>
function updatePriorityColor(select) {
    const indicator = document.getElementById('priority-indicator');
    const colorClass = select.options[select.selectedIndex].dataset.color;
    indicator.classList.remove('red-dot','yellow-dot','blue-dot');
    if (colorClass) indicator.classList.add(colorClass);
}
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('priority');
    if (sel) updatePriorityColor(sel);
});
</script>
</body>
</html>