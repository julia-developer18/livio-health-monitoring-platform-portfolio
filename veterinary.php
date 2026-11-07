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
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к базе: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $animal = trim($_POST['animal'] ?? '');
    $breed = trim($_POST['breed'] ?? '');
    $pet_info = trim($_POST['pet_info'] ?? '');
    $service_date = $_POST['service_date'] ?? '';
    $meeting_place = trim($_POST['meeting_place'] ?? '');
    $wishes = trim($_POST['wishes'] ?? '');
    $photoPath = null;

    if ($animal === '') {
        $errors[] = 'Укажите животное';
    }
    if ($service_date === '') {
        $errors[] = 'Укажите дату и время услуги';
    }
    if ($meeting_place === '') {
        $errors[] = 'Укажите место встречи';
    }

    // Проверка и загрузка файла (если был)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg','image/png','application/pdf'];
        if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
            $errors[] = 'Файл должен быть изображением (JPEG, PNG) или PDF';
        } else {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photoName = uniqid('veterinary_', true) . '.' . $ext;
            $photoPathFull = $uploadDir . $photoName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPathFull)) {
                $photoPath = 'uploads/' . $photoName;
            } else {
                $errors[] = 'Ошибка загрузки файла';
            }
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO veterinary_requests 
            (user_id, animal, breed, pet_info, service_date, meeting_place, wishes, photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, $animal, $breed, $pet_info, $service_date, $meeting_place, $wishes, $photoPath
        ]);
        $success = 'Заявка на вызов ветеринара успешно отправлена!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
                <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

            <link rel="icon" href="/icons/livio-main-logo.png" type="image/x-icon">


    <title>Ветеринар</title>
</head>
<body class="font-['Roboto'] bg-linear-to-r from-[#007F3C] to-[#007256] ">
<header>
        <a href="services.php" class="absolute top-6 left-6"><img src="icons/arrow-back-main_page.png" alt=""></a>
        <img src="images/pet-vet-icon.png" alt="" class="absolute top-12 right-6">
        <h1 class="text-[#F7E643] font-bold text-4xl pl-8 mt-34">Ветеринар</h1>
</header>
<main class="bg-white rounded-t-4xl py-6 mt-4">
    <section>
        <h2 class="pl-8 text-3xl font-bold py-6">Главное о задаче</h2>
        <div class="">
            <?php if ($errors): ?>
            <div class="mb-4 mx-auto max-w-lg p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?=htmlspecialchars($e)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="mb-4 mx-auto max-w-lg p-4 bg-green-100 text-green-700 rounded">
                <?=htmlspecialchars($success)?>
            </div>
            <?php endif; ?>
            <form class="max-w-lg mx-auto p-6 px-8" method="post" enctype="multipart/form-data">
               
                <div class="mb-4">
                    <label for="animal" class="block mb-2 font-semibold">Какое у вас животное?</label>
                    <input type="text" id="animal" name="animal" value="<?=htmlspecialchars($_POST['animal'] ?? '')?>"
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007256]" 
                        placeholder="Например, собака, кошка" required>
                </div>
              
                <div class="mb-4">
                    <label for="breed" class="block mb-2 font-semibold">Укажите породу</label>
                    <input type="text" id="breed" name="breed" 
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007256]" 
                        placeholder="Порода животного"
                        value="<?=htmlspecialchars($_POST['breed'] ?? '')?>">
                </div>

                <div class="mb-4">
                    <label for="pet_info" class="block mb-2 font-semibold">Данные о питомце</label>
                    <textarea id="pet_info" name="pet_info" rows="3"
                              class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007256]" 
                              placeholder="Возраст, вес, состояние и т.п."><?=htmlspecialchars($_POST['pet_info'] ?? '')?></textarea>
                </div>

                <div class="mb-4">
                    <label for="service_date" class="block mb-2 font-semibold">Когда нужна услуга?</label>
                    <input type="datetime-local" id="service_date" name="service_date" value="<?=htmlspecialchars($_POST['service_date'] ?? '')?>" 
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007256]" required>
                </div>

                <div class="mb-4">
                    <label for="meeting_place" class="block mb-2 font-semibold">Где вам удобно встретиться?</label>
                    <input type="text" id="meeting_place" name="meeting_place" value="<?=htmlspecialchars($_POST['meeting_place'] ?? '')?>"
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007256]" 
                        placeholder="Адрес или ориентиры" required>
                </div>

                <h3 class="text-3xl font-bold py-6">Дополнительно</h3>

                <div class="mb-4">
                    <label for="wishes" class="block mb-2 font-semibold">Пожелания и особенности</label>
                    <textarea id="wishes" name="wishes" rows="3"
                              class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007256]" 
                              placeholder="Особенности питомца, пожелания к услуге"><?=htmlspecialchars($_POST['wishes'] ?? '')?></textarea>
                </div>

                <div class="mb-6">
                    <label for="photo" class="block mb-2 font-semibold">Фото или документ</label>
                    <input type="file" id="photo" name="photo" accept="image/*,.pdf"
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007256]">
                </div>

                <button type="submit" class="w-full bg-linear-to-r from-[#007F3C] to-[#007256] text-white py-4 px-10 rounded-2xl font-semibold hover:bg-[#007256] transition">
                    Отправить заявку
                </button>
            </form>
        </div>
    </section>
</main>
<script src="js/decoration.js"></script>
</body>
</html>