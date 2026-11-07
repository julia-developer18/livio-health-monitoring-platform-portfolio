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
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$errors = [];
$success = '';


$stmt = $pdo->prepare("SELECT * FROM water_intake WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $today]);
$water = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$water) {
    
    $goal_ml = 2000; 
    $consumed_ml = 0;

    
    $stmt = $pdo->prepare("INSERT INTO water_intake (user_id, date, goal_ml, consumed_ml) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $today, $goal_ml, $consumed_ml]);
    $water = ['goal_ml' => $goal_ml, 'consumed_ml' => $consumed_ml];
} else {
    $goal_ml = (int)$water['goal_ml'];
    $consumed_ml = (int)$water['consumed_ml'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_goal'])) {
        $new_goal = (int)($_POST['goal_ml'] ?? 0);
        if ($new_goal < 500 || $new_goal > 10000) {
            $errors[] = "Цель должна быть от 500 до 10000 мл.";
        } else {
            $goal_ml = $new_goal;
            $stmt = $pdo->prepare("UPDATE water_intake SET goal_ml = ? WHERE user_id = ? AND date = ?");
            $stmt->execute([$goal_ml, $user_id, $today]);
            $success = "Цель обновлена.";
        }
    }

  
    if (isset($_POST['mark_drink'])) {
        $change = (int)($_POST['change_ml'] ?? 0);
        $new_consumed = $consumed_ml + $change;

        if ($new_consumed < 0) $new_consumed = 0;
        if ($new_consumed > $goal_ml) $new_consumed = $goal_ml;

        $consumed_ml = $new_consumed;
        $stmt = $pdo->prepare("UPDATE water_intake SET consumed_ml = ? WHERE user_id = ? AND date = ?");
        $stmt->execute([$consumed_ml, $user_id, $today]);

        $success = "Данные обновлены.";
    }
}


$glass_volume = 250;
$total_glasses = (int)ceil($goal_ml / $glass_volume); 
$filled_glasses = (int)floor($consumed_ml / $glass_volume);
    ?>

    <!DOCTYPE html>
    <html lang="ru" class="scroll-smooth">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

            <link rel="icon" href="/icons/livio-main-logo.png" type="image/x-icon">

            <link rel="stylesheet" href="css/style.css">
    <title>Вода</title>

    </head>
    <style>
            .glass-button {
            background: linear-gradient(180deg, #d6e6f9 0%, #3b82f6 60%, #1e40af 100%);
            border-radius: 0 0 1.5rem 1.5rem;
            width: 64px;  
            height: 144px; 
            border: 2px solid #3b82f6;
            cursor: pointer;
            position: relative;
            clip-path: polygon(
            20% 0%, 80% 0%, 
            100% 100%, 0% 100% 
            );
            transition: background-color 0.3s ease, border-color 0.3s ease;
            overflow: hidden;
        }

        .glass-button:disabled {
            background: #e0e7ff;
            border-color: #a5b4fc;
            cursor: not-allowed;
        }

        .glass-liquid {
            position: absolute;
            bottom: 0;
            left: 10%;
            width: 80%;
            height: 0%;
            background: linear-gradient(180deg, #60a5fa, #1e40af);
            border-radius: 0 0 1.2rem 1.2rem;
            transition: height 0.4s ease;
            z-index: 1;
        }

        .glass-button.filled .glass-liquid {
            height: 75%; /* Высота залитой воды в стакане */
        }

        .glass-button.empty:hover:not(:disabled) {
            background-color: #93c5fd;
            border-color: #2563eb;
        }
</style>

<body class="bg-linear-to-r from-[#49B7C7] to-[#31557E] font-['Roboto'] ">

<header class="px-6">
    <a href="account.php" class="absolute top-6 left-6"><img src="/icons/arrow-white.png" alt=""></a>
        <img src="/icons/water.png" alt="" class="absolute top-12 right-6">
        <h1 class=" font-bold text-white text-[28px] mt-35 text-[#4D4D4D]">Потребление воды</h1>
    <p class=" text-white font-light font-lg">Отметьте, сколько вы выпили сегодня</p>
</header>

<main class="max-w-md w-full bg-[#F2FDFF] rounded-4xl shadow mt-6 py-8 px-8 mb-4">

    <form method="post" class="flex items-center mb-10 gap-1">
        <label for="goal_ml" class="font-medium text-[#706E6E]">Цель (мл):</label>
        <input type="number" name="goal_ml" id="goal_ml" min="500" max="10000" step="50" value="<?=htmlspecialchars($goal_ml)?>" class="border border-[#31557E] rounded-xl text-[#A8A8A8] focus:outline-none focus:ring-2 focus:ring-[#4299B1] p-1" required />
        <button type="submit" name="set_goal" class="ml-auto bg-[#31557E] text-white r rounded-xl p-2 hover:bg-[#356DAD] transition">Сохранить цель</button>
    </form>

  
    <div class="grid grid-cols-4 gap-4 justify-center">
    <?php for ($i = 1; $i <= $total_glasses; $i++): ?>
        <form method="post" class="flex flex-col items-center">
            <input type="hidden" name="change_ml" value="<?= $glass_volume ?>" />
            <button 
                type="submit" 
                name="mark_drink" 
                class="glass-button relative w-16 h-36 transition-colors focus:outline-none <?= $i <= $filled_glasses ? 'filled' : 'empty' ?>"
                title="Добавить стакан воды"
                <?= $i > $filled_glasses + 1 ? 'disabled' : '' ?>
            >
               
                <div class="glass-liquid"></div>
            </button>
        </form>
    <?php endfor; ?>
    </div>

    <p class="mt-4 text-center text-[#31557E] font-semibold">Выпито <?= $consumed_ml ?> мл из <?= $goal_ml ?> мл</p>

  
    <form method="post" class="mt-4 flex justify-center gap-4">
        <input type="hidden" name="change_ml" value="-250" />
        <button type="submit" name="mark_drink" class="bg-[#31557E] text-white py-4 px-8 rounded-2xl hover:bg-[#356DAD] transition">
            Удалить стакан
        </button>
    </form>

</main>

</body>
</html>