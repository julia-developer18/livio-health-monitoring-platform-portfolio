<?php
session_start();

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
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^\+7\d{10}$/', $phone)) {
        $errors[] = "Введите корректный номер телефона, начинающийся с +7.";
    }

    if (empty($password)) {
        $errors[] = "Введите пароль.";
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_photo'] = $user['photo'];

            header("Location: account.php");
            exit;
        } else {
            $errors[] = "Неправильный номер телефона или пароль.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />


<link rel="icon" href="./icons/livio-main-logo.png" type="image/x-icon">

    <title>Вход</title>
</head>
<body class="bg-[#EDEDED] font-['Roboto']">
<main class="bg-[#EDEDED]">
    <section class="bg-[#BD363A] relative w-full h-48">
        <a href="./index.php" class="absolute top-6 right-6"><img src="icons/arrow-back-main_page.png" alt=""></a>
        <h1 class="text-white text-6xl font-black pl-10 pt-10">livio</h1>
        <h2 class="text-white text-4xl font-medium pl-10 mt-4">Вход</h2>
    </section>
    <div class="relative overflow-hidden -mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#BD363A" fill-opacity="1" d="M0,96L48,117.3C96,139,192,181,288,186.7C384,192,480,160,576,128C672,96,768,64,864,64C960,64,1056,96,1152,138.7C1248,181,1344,235,1392,261.3L1440,288L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>
    </div>
    <section class="bg-[#EDEDED]">
                <?php if ($errors): ?>
        <ul style="color: red;" class="max-w-md mx-auto text-center">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <form action="" method="post" class="p-6 max-w-md mx-auto space-y-6">
            <div class="flex flex-col">
                <label for="phone" class="mb-2 font-semibold text-gray-700 text-xl">Номер телефона</label>
                <input id="phone" name="phone" type="text" placeholder="+7XXXXXXXXXX" value="<?= htmlspecialchars($phone) ?>"
                       class="rounded-lg shadow-md border-gray-300 border py-3 focus:outline-none focus:ring-2 focus:ring-[#C63024] text-xl px-3" required />
            </div>
            <div class="flex flex-col">
                <label for="password" class="mb-2 font-semibold text-gray-700 text-xl">Пароль</label>
                <input id="password" name="password" type="password" 
                       class="rounded-lg shadow-md border-gray-300 border py-3 focus:outline-none focus:ring-2 focus:ring-[#C63024] text-xl px-3" required />
            </div>
            <button type="submit" class="bg-[#C63024] text-white font-semibold text-xl py-6 px-26 rounded-3xl hover:bg-[#a5231b] transition-colors block mx-auto">
                Войти
            </button>
            <p class="text-[#A6A6A6] text-center">Нажимая кнопку, вы подтверждаете согласие на обработку своих персональных данных</p>
        </form>

    </section>
</main>
</body>
</html>