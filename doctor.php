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
    $patient_name = trim($_POST['patient_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';

    if ($patient_name === '') {
        $errors[] = 'Введите имя пациента';
    }
    if ($phone === '') {
        $errors[] = 'Введите телефон';
    }
    if ($appointment_date === '') {
        $errors[] = 'Введите дату приёма';
    }
    if ($appointment_time === '') {
        $errors[] = 'Выберите время приёма';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO doctor_appointments (user_id, patient_name, phone, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $patient_name, $phone, $appointment_date, $appointment_time]);
        $success = 'Вы успешно записаны к врачу!';
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

</head>
<body class="font-['Roboto'] bg-linear-to-t from-[#4C4C4C] to-[#DDDBDF] bg-no-repeat bg-cover bg-center ">
<header>
    <a href="./account.php" class="absolute top-6 left-6"><img src="icons/arrow-back-main_page.png" alt=""></a>
        <img src="./images/doctor-sos.png" alt="" class="absolute top-12 right-6">
        <h1 class=" font-bold text-4xl pl-8 mt-40">Запись к врачу</h1>
    <p class="mt-4 pl-8 text-[#3F3F3F]">Пожалуйста, заполните следующие<br> поля, чтобы мы смогли связаться с <br>вами и записать на удобное время.</p>
</header>
<main class="bg-white rounded-t-4xl py-6 mt-4">
    <section>
        <div class="px-8 mt-4 max-w-lg mx-auto">
            <?php if ($errors): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                        <li><?=htmlspecialchars($e)?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    <?=htmlspecialchars($success)?>
                </div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-4">
                    <label for="patient_name" class="block mb-2 font-semibold">Имя пациента</label>
                    <input type="text" id="patient_name" name="patient_name" value="<?=htmlspecialchars($_POST['patient_name'] ?? '')?>"
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4C4C4C]" 
                        placeholder="Введите имя пациента" required>
                </div>

                <div class="mb-4">
                    <label for="phone" class="block mb-2 font-semibold">Телефон для связи</label>
                    <input type="tel" id="phone" name="phone" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>"
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4C4C4C]" 
                        placeholder="Введите телефон" required>
                </div>

                <div class="mb-4">
                    <label for="appointment_date" class="block mb-2 font-semibold">Дата приёма</label>
                    <input type="date" id="appointment_date" name="appointment_date" value="<?=htmlspecialchars($_POST['appointment_date'] ?? '')?>"
                        class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4C4C4C]"
                        required>
                </div>

                <div class="mb-6">
                    <label for="appointment_time" class="block mb-2 font-semibold">Время приёма</label>
                    <select id="appointment_time" name="appointment_time" class="w-full py-4 px-6 bg-[#F2F3F8] rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4C4C4C]" required>
                        <option value="" disabled <?=(!isset($_POST['appointment_time'])) ? 'selected' : ''?>>Выберите время приёма</option>
                        <?php
                        $times = ["08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00"];
                        foreach($times as $time):
                        ?>
                          <option value="<?= $time ?>" <?= (($_POST['appointment_time'] ?? '') === $time) ? 'selected' : '' ?>><?= $time ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#C63024] text-white py-4 px-10 rounded-2xl font-semibold hover:bg-[#9F2218] transition">
                    Записаться
                </button>
            </form>
        </div>
    </section>
</main>
</body>
</html>