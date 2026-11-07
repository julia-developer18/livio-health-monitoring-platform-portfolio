<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$host = 'localhost';
$dbname = 'mysite';
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

$errors = [];


if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM metrics WHERE id = ? AND user_id = ?");
        $stmt->execute([$delete_id, $_SESSION['user_id']]);
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"],'?'));
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $date = $_POST['date'] ?? date('Y-m-d');
    $weight = $_POST['weight'] ?? null;
    $height = $_POST['height'] ?? null;
    $waist = $_POST['waist'] ?? null;
    $chest = $_POST['chest'] ?? null;
    $hips = $_POST['hips'] ?? null;

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $errors[] = "Введите корректную дату.";
    }

    $fields = ['weight' => $weight, 'height' => $height, 'waist' => $waist, 'chest' => $chest, 'hips' => $hips];
    foreach ($fields as $field => $val) {
        if ($val !== '' && !is_null($val) && !preg_match('/^\d+(\.\d{1,2})?$/', $val)) {
            $errors[] = "Поле '$field' должно быть числом (с точностью до 2 знаков).";
        }
    }

    if (!$errors) {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE metrics SET date = ?, weight = ?, height = ?, waist = ?, chest = ?, hips = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$date, $weight ?: null, $height ?: null, $waist ?: null, $chest ?: null, $hips ?: null, $id, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO metrics (user_id, date, weight, height, waist, chest, hips) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $date, $weight ?: null, $height ?: null, $waist ?: null, $chest ?: null, $hips ?: null]);
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM metrics WHERE user_id = ? ORDER BY date DESC");
$stmt->execute([$_SESSION['user_id']]);
$metrics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<link rel="icon" href="/icons/livio-main-logo.png" type="image/x-icon">


<title>Метрические данные</title>
</head>
<body class="bg-linear-to-r from-[#ECE7F9] to-[#8A859A] font-['Roboto'] py-4">

<header class="px-6">
    <a href="./account.php" class="absolute top-6 left-6"><img src="/icons/arrow-gray.png" alt=""></a>
    <img src="./icons/category-metrik-icon.png" alt="" class="absolute top-12 right-6">
    <h1 class="font-bold text-[28px] mt-40 text-[#4D4D4D]">Метрические данные</h1>
    <p class="mt-2 text-[#666666] text-sm">Добавляйте, изменяйте и просматривайте ваши параметры</p>
</header>

<main class="mt-6 bg-[#F5F1FF] rounded-3xl py-10 ">
    <?php if ($errors): ?>
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="space-y-6 px-8 ">
        <?php if (count($metrics) === 0): ?>
            <p class="text-center text-[#9A9A9A] py-8">Пока нет данных</p>
        <?php else: ?>
            <?php foreach ($metrics as $metric): ?>
                
                <div class="rounded-2xl p-4 bg-white shadow">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-medium text-[#4D4D4D]"><?= htmlspecialchars($metric['date']) ?></div>
                        <button class="editBtn text-[#8A849A] hover:underline" 
                            data-id="<?= $metric['id'] ?>"
                            data-date="<?= $metric['date'] ?>"
                            data-weight="<?= htmlspecialchars($metric['weight'] ?? '') ?>"
                            data-height="<?= htmlspecialchars($metric['height'] ?? '') ?>"
                            data-waist="<?= htmlspecialchars($metric['waist'] ?? '') ?>"
                            data-chest="<?= htmlspecialchars($metric['chest'] ?? '') ?>"
                            data-hips="<?= htmlspecialchars($metric['hips'] ?? '') ?>"
                        >Редактировать</button>

                    <button 
                        class="delete-btn" 
                        onclick="if(confirm('Вы уверены, что хотите удалить эту запись?')) { window.location='?delete_id=<?= $metric['id'] ?>'; }" 
                        aria-label="Удалить запись"
                        type="button"
                    >
                    <img src="/icons/krest.png" alt="" class="w-4 h-4">
                    </button>


                    </div>



                    <div class="grid grid-cols-2 gap-2 text-[#6B6B6B] font-light mt-1">
                        <div><span class="font-normal">Вес:</span> <?= $metric['weight'] ?? '-' ?> кг</div>
                        <div><span class="font-normal">Рост:</span> <?= $metric['height'] ?? '-' ?> см</div>
                        <div><span class="font-normal">Талия:</span> <?= $metric['waist'] ?? '-' ?> см</div>
                        <div><span class="font-normal">Грудь:</span> <?= $metric['chest'] ?? '-' ?> см</div>
                        <div><span class="font-normal">Бёдра:</span> <?= $metric['hips'] ?? '-' ?> см</div>
                    </div>


                </div>

                
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button id="addMetricBtn" class="mt-10 bg-[#8A849A] text-white py-4 px-8 rounded-2xl hover:bg-[#6E6782] transition block mx-auto">Добавить запись</button>
</main>

<div id="metricModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-3xl shadow-lg w-full max-w-md p-6 relative">
        <button id="closeModalBtn" aria-label="Закрыть" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 transition text-2xl font-bold">&times;</button>
        <h2 id="modalTitle" class="text-2xl font-medium mb-6 text-center text-[#4D4D4D]">Добавить запись</h2>
        
        <form id="metricForm" method="post" class="space-y-4" novalidate>
            <input type="hidden" name="id" id="metricId" value="">

            <div>
                <label for="date" class="block font-medium mb-1 text-[#4D4D4D]">Дата *</label>
                <input type="date" id="date" name="date" required class="w-full border border-[#8A849A] rounded-xl py-4 px-8 text-[#6B6B6B]" />
            </div>

            <div>
                <label for="weight" class="block font-medium mb-1 text-[#4D4D4D]">Вес (кг)</label>
                <input type="text" id="weight" name="weight" pattern="^\d+(\.\d{1,2})?$" title="Число, до 2 знаков" placeholder="например 70.5" class="w-full border border-[#8A849A] rounded-xl py-4 px-8 text-[#6B6B6B]" />
            </div>

            <div>
                <label for="height" class="block font-medium mb-1 text-[#4D4D4D]">Рост (см)</label>
                <input type="text" id="height" name="height" pattern="^\d+(\.\d{1,2})?$" title="Число, до 2 знаков" placeholder="например 175" class="w-full border border-[#8A849A] rounded-xl py-4 px-8 text-[#6B6B6B]" />
            </div>

            <div>
                <label for="waist" class="block font-medium mb-1 text-[#4D4D4D]">Обхват талии (см)</label>
                <input type="text" id="waist" name="waist" pattern="^\d+(\.\d{1,2})?$" title="Число, до 2 знаков" class="w-full border border-[#8A849A] rounded-xl py-4 px-8 text-[#6B6B6B]" />
            </div>

            <div>
                <label for="chest" class="block font-medium mb-1 text-[#4D4D4D]">Обхват груди (см)</label>
                <input type="text" id="chest" name="chest" pattern="^\d+(\.\d{1,2})?$" title="Число, до 2 знаков" class="w-full border border-[#8A849A] rounded-xl py-4 px-8 text-[#6B6B6B]" />
            </div>

            <div>
                <label for="hips" class="block font-medium mb-1 text-[#4D4D4D]">Обхват бёдер (см)</label>
                <input type="text" id="hips" name="hips" pattern="^\d+(\.\d{1,2})?$" title="Число, до 2 знаков" class="w-full border border-[#8A849A] rounded-xl py-4 px-8 text-[#6B6B6B]" />
            </div>

            <button type="submit" class="w-full bg-[#8A849A] hover:bg-[#6E6782] text-white py-3 rounded-2xl font-semibold transition">Сохранить</button>
        </form>
    </div>
</div>

<script>
const addMetricBtn = document.getElementById('addMetricBtn');
const metricModal = document.getElementById('metricModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const modalTitle = document.getElementById('modalTitle');
const metricForm = document.getElementById('metricForm');

const metricIdInput = document.getElementById('metricId');
const dateInput = document.getElementById('date');
const weightInput = document.getElementById('weight');
const heightInput = document.getElementById('height');
const waistInput = document.getElementById('waist');
const chestInput = document.getElementById('chest');
const hipsInput = document.getElementById('hips');

addMetricBtn.addEventListener('click', () => {
    modalTitle.textContent = 'Добавить запись';
    metricIdInput.value = '';
    metricForm.reset();
    dateInput.value = new Date().toISOString().slice(0,10);
    metricModal.classList.remove('hidden');
});

closeModalBtn.addEventListener('click', () => {
    metricModal.classList.add('hidden');
    metricForm.reset();
});

window.addEventListener('click', e => {
    if (e.target === metricModal) {
        metricModal.classList.add('hidden');
        metricForm.reset();
    }
});

document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        modalTitle.textContent = 'Редактировать запись';
        metricForm.reset();
        metricModal.classList.remove('hidden');

        metricIdInput.value = btn.getAttribute('data-id');
        dateInput.value = btn.getAttribute('data-date');
        weightInput.value = btn.getAttribute('data-weight');
        heightInput.value = btn.getAttribute('data-height');
        waistInput.value = btn.getAttribute('data-waist');
        chestInput.value = btn.getAttribute('data-chest');
        hipsInput.value = btn.getAttribute('data-hips');
    });
});
</script>

</body>
</html>