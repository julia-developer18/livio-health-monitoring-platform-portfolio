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
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Добавление
    if (isset($_POST['add_dish'])) {
        $dishName = trim($_POST['dish_name'] ?? '');
        $photo = $_FILES['dish_photo'] ?? null;

        if ($dishName === '') {
            $errors[] = "Введите название блюда.";
        }
        if (!$photo || $photo['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Загрузите фото блюда.";
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($photo['type'], $allowedTypes)) {
                $errors[] = "Фото блюда должно быть в формате JPEG, PNG или GIF.";
            }
        }
        if (!$errors) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $photoName = uniqid('dish_', true) . '.' . $ext;
            $photoPath = $uploadDir . $photoName;
            if (move_uploaded_file($photo['tmp_name'], $photoPath)) {
                $stmt = $pdo->prepare("INSERT INTO dishes (user_id, name, photo) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $dishName, 'uploads/' . $photoName]);
                $success = "Блюдо успешно добавлено.";
            } else {
                $errors[] = "Ошибка при загрузке фото.";
            }
        }
    }

    if (isset($_POST['edit_dish'])) {
        $dishId = (int)($_POST['dish_id'] ?? 0);
        if ($dishId <= 0) {
            $errors[] = "Неверный ID блюда.";
        } else {
            $dishName = trim($_POST['dish_name'] ?? '');
            if ($dishName === '') {
                $errors[] = "Введите название блюда.";
            }
            $stmt = $pdo->prepare("SELECT * FROM dishes WHERE id = ? AND user_id = ?");
            $stmt->execute([$dishId, $_SESSION['user_id']]);
            $dish = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$dish) {
                $errors[] = "Блюдо не найдено или доступ запрещён.";
            }
            $newPhotoPath = $dish['photo'];
            if (isset($_FILES['dish_photo']) && $_FILES['dish_photo']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['dish_photo']['type'], $allowedTypes)) {
                    $errors[] = "Фото блюда должно быть в формате JPEG, PNG или GIF.";
                } else {
                    $uploadDir = __DIR__ . '/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $ext = pathinfo($_FILES['dish_photo']['name'], PATHINFO_EXTENSION);
                    $photoName = uniqid('dish_', true) . '.' . $ext;
                    $photoPath = $uploadDir . $photoName;
                    if (move_uploaded_file($_FILES['dish_photo']['tmp_name'], $photoPath)) {
                        $newPhotoPath = 'uploads/' . $photoName;
                    } else {
                        $errors[] = "Ошибка при загрузке фото.";
                    }
                }
            }
            if (!$errors) {
                $stmt = $pdo->prepare("UPDATE dishes SET name = ?, photo = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$dishName, $newPhotoPath, $dishId, $_SESSION['user_id']]);
                $success = "Блюдо успешно обновлено.";
            }
        }
    }
}


$stmt = $pdo->prepare("SELECT * FROM dishes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Питание</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />

 <link rel="icon" href="./icons/livio-main-logo.png" type="image/x-icon">

</head>
<body class="font-['Roboto'] bg-linear-to-r from-[#F18E65] to-[#C45B55] ">

<header>
    <a href="account.php" class="absolute top-6 left-6"><img src="icons/arrow-back-main_page.png" alt="Назад"></a>
    <img src="icons/feed-icon.png" alt="" class="absolute top-12 right-6">
    <h1 class="text-[#F7E643] font-bold text-4xl pl-8 mt-30">Питание</h1>
</header>

<main class="bg-[#F6FFF8] rounded-t-4xl py-6 mt-4">
    <section>
        <h2 class="pl-8 text-3xl font-bold py-6">Меню недели</h2>
        <div class="max-w-sm w-full rounded-lg overflow-visible pl-8">
            <div id="carousel-wrapper" class="relative overflow-hidden">
                <ul id="carousel" class="flex transition-transform duration-300 ease-in-out gap-x-4">
                    <li class="flex-shrink-0 w-1/2 select-none rounded-lg overflow-hidden ">
                        <img src="./images/food-1.png" alt="Котёнок №1" class="w-full object-cover" />
                        <p class="text-center text-gray-800 font-light py-2">Рыба с брокколями<br> и яйцом</p>
                    </li>
                    <li class="flex-shrink-0 w-1/2 select-none rounded-lg overflow-hidden ">
                        <img src="./images/food-2.png" alt="Котёнок №2" class="w-full object-cover" />
                        <p class="text-center text-gray-800 font-light py-2">ПП запеканка из <br> кабачков в духовке</p>
                    </li>
                    <li class="flex-shrink-0 w-1/2 select-none rounded-lg overflow-hidden ">
                        <img src="./images/food-3.png" alt="Котёнок №3" class="w-full object-cover" />
                        <p class="text-center text-gray-800 font-light py-2">ПП гуляш из <br> курицы</p>
                    </li>
                    <li class="flex-shrink-0 w-1/2 select-none rounded-lg overflow-hidden ">
                        <img src="./images/food-1.png" alt="Котёнок №4" class="w-full object-cover" />
                        <p class="text-center text-gray-800 font-light py-2">Рыба с брокколями<br> и яйцом</p>
                    </li>
                </ul>
            </div>
        </div>
    </section>

        <section class="">

            <?php if ($errors): ?>
            <div class="max-w-md mx-auto my-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif($success): ?>
            <div class="max-w-md mx-auto my-4 p-4 bg-green-100 text-green-700 rounded">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>


        <h2 class="text-3xl font-bold py-6 pl-8">Готовили дома</h2>
        <div class="flex gap-4 px-8 justify-center">
            <?php foreach ($dishes as $dish): ?>
                <div class="flex flex-col items-center cursor-pointer" data-dish-id="<?= $dish['id'] ?>" data-dish-name="<?= htmlspecialchars($dish['name'], ENT_QUOTES) ?>" data-dish-photo="<?= htmlspecialchars($dish['photo'], ENT_QUOTES) ?>">
                    <div class="rounded-3xl w-[150px] h-[150px] overflow-hidden border border-gray-300">
                        <img src="<?= htmlspecialchars($dish['photo']) ?>" alt="<?= htmlspecialchars($dish['name']) ?>" class="w-full h-full object-cover" />
                    </div>
                    <p class="text-center text-gray-800 font-light py-2 w-[150px] truncate"><?= htmlspecialchars($dish['name']) ?></p>
                </div>
            <?php endforeach; ?>

            <?php
           
            $emptyCount = max(0, 2 - count($dishes));
            for ($i = 0; $i < $emptyCount; $i++):
            ?>
                <div class="flex flex-col items-center cursor-pointer" data-dish-id="" data-dish-name="" data-dish-photo="">
                    <div class="bg-[#BBBBBD] h-[150px] w-[150px] rounded-3xl flex items-center justify-center hover:bg-gray-400 transition" title="Добавить блюдо">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <p class="text-center text-gray-800 font-light py-2 w-[150px] truncate">Название блюда</p>
                </div>
            <?php endfor; ?>
        </div>
    </section>

    <section class="py-6">
        <h2 class="pl-8 text-3xl font-bold py-6">Рекомендации врача</h2>
        <button type="submit" id="openModalBtn" class="bg-[#C63024] text-white rounded-3xl py-4 text-xl font-bold px-6 block mx-auto">Посмореть рекомендации</button>

        <div id="modal" class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-sm w-4/5 p-6 relative">
             
                <button id="closeModalBtn" aria-label="Закрыть" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 transition text-2xl font-bold">
                    &times;
                </button>
                <p class="text-center text-gray-800 text-lg font-semibold">
                    Данная функция находится в разработке
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-t-4xl pt-6 mt-8" style="box-shadow: 0 -4px 6px -1px rgba(0,0,0,0.1), 0 -2px 4px -2px rgba(0,0,0,0.06);">
        <img src="icons/arrow-top.png" alt="" class="block mx-auto py-2" id="jumpingImg">
        <h4 class="text-2xl text-center">Воспользуйтесь нашем ИИ</h4>
        <p class="text-[#A0A0A0] text-center text-sm mt-2">И мы обязательно вам поможем выбрать что-то вкусное</p>
    </section>
</main>


<div id="dishModal" class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-4/5 p-6 relative">
        <button id="closeDishModal" aria-label="Закрыть" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 transition text-2xl font-bold">&times;</button>
        <h3 id="dishModalTitle" class="text-center text-gray-800 text-lg font-semibold mb-4">Добавить блюдо</h3>
        <form id="dishForm" method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
            <input type="hidden" name="dish_id" id="dishIdInput" value="" />
            <input type="text" name="dish_name" id="dishNameInput" placeholder="Название блюда" required class="border rounded p-2" />
            <input type="file" name="dish_photo" id="dishPhotoInput" accept="image/*" />
            <p class="text-sm text-gray-500">При редактировании фото можно не менять</p>
            <button type="submit" name="" id="submitDishBtn" class="bg-[#C45B55] hover:bg-[#a44443] transition text-white py-2 px-4 rounded-3xl text-lg">Сохранить</button>
        </form>
    </div>
</div>



<script>

const dishModal = document.getElementById('dishModal');
const closeDishModalBtn = document.getElementById('closeDishModal');
const dishForm = document.getElementById('dishForm');
const dishModalTitle = document.getElementById('dishModalTitle');
const dishIdInput = document.getElementById('dishIdInput');
const dishNameInput = document.getElementById('dishNameInput');
const dishPhotoInput = document.getElementById('dishPhotoInput');
const submitDishBtn = document.getElementById('submitDishBtn');

document.querySelectorAll('div[data-dish-id]').forEach(block => {
    block.addEventListener('click', () => {
        const dishId = block.getAttribute('data-dish-id');
        const dishName = block.getAttribute('data-dish-name');
        const dishPhoto = block.getAttribute('data-dish-photo');

        dishModal.classList.remove('hidden');

        if (dishId) {
            dishModalTitle.textContent = 'Редактировать блюдо';
            dishIdInput.value = dishId;
            dishNameInput.value = dishName || '';
            dishPhotoInput.value = '';
            submitDishBtn.name = 'edit_dish';
            submitDishBtn.textContent = 'Сохранить';
            dishPhotoInput.required = false;
        } else {
            dishModalTitle.textContent = 'Добавить блюдо';
            dishIdInput.value = '';
            dishNameInput.value = '';
            dishPhotoInput.value = '';
            submitDishBtn.name = 'add_dish';
            submitDishBtn.textContent = 'Добавить';
            dishPhotoInput.required = true;
        }
    });
});

closeDishModalBtn.addEventListener('click', () => {
    dishModal.classList.add('hidden');
    dishForm.reset();
});


window.addEventListener('click', e => {
    if (e.target === dishModal) {
        dishModal.classList.add('hidden');
        dishForm.reset();
    }
});


const openModalBtn = document.getElementById("openModalBtn");
const modal = document.getElementById("modal");
const closeModalBtn = document.getElementById("closeModalBtn");

openModalBtn.addEventListener("click", () => {
    modal.classList.remove("hidden");
});
closeModalBtn.addEventListener("click", () => {
    modal.classList.add("hidden");
});
window.addEventListener("click", e => {
    if (e.target === modal) {
        modal.classList.add("hidden");
    }
});
</script>

<script src="js/carousel.js"></script>
<script src="js/decoration.js"></script>
<script src="js/error.js"></script>

</body>
</html>