<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$name = htmlspecialchars($_SESSION['user_name'] ?? 'Пользователь');
$photo = htmlspecialchars($_SESSION['user_photo'] ?? 'icons/default-user.png');

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


$stmtVet = $pdo->prepare("SELECT * FROM veterinary_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmtVet->execute([$user_id]);
$veterinaryRequests = $stmtVet->fetchAll(PDO::FETCH_ASSOC);


$stmtDoc = $pdo->prepare("SELECT * FROM doctor_appointments WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmtDoc->execute([$user_id]);
$doctorAppointments = $stmtDoc->fetchAll(PDO::FETCH_ASSOC);


$totalNotifications = count($veterinaryRequests) + count($doctorAppointments);
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

    <link rel="icon" href="/icons/livio-main-logo.png" type="image/x-icon">

    <link rel="stylesheet" href="./css/style.css" />
    <title>livio</title>
    <style>
        
        .user-photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        #notifModal {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="font-['Roboto'] bg-[#E1DFE4]">

<header class="bg-white pt-20 pb-12 rounded-b-4xl">
    <div class="flex justify-around place-items-center ">
        <h1 class="text-6xl font-black">livio</h1>
        <div class="flex gap-6">
            <a href="#" id="bell" class="relative inline-block">
                <img src="icons/notify.png" alt="Уведомления">
                <?php if ($totalNotifications > 0): ?>
                    <span
                        class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-semibold"
                        title="Новые уведомления">
                        <?= $totalNotifications ?>
                    </span>
                <?php endif; ?>
            </a>
           <a href="#" id="searchIcon"><img src="./icons/search.png" alt="Поиск"></a>
        </div>
    </div>

    <div id="searchContainer" class="max-w-md mx-auto mt-4 px-6 hidden">
    <input
        type="text"
        id="siteSearch"
        placeholder="Введите название — например, Метрические данные"
        class="w-full pl-4 pr-4 py-3 rounded-xl border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#BD363A] focus:border-transparent font-['Roboto']"
    />
    </div>



    <div class="flex gap-3 px-6 mt-10 justify-center">
        <a href="./pharmacy.php" id="pharmacy-link">
            <div class="bg-linear-to-r from-[#C93133] to-[#9D557A] rounded-3xl p-2">
                <img src="./icons/pharmacy.png" alt="Аптека" class="pl-8" />
                <h2 class="text-white text-lg pl-2 mt-2">Аптека</h2>
            </div>
        </a>


        <a href="./feed.php">
            <div class="bg-linear-to-r from-[#E98769] to-[#962746] rounded-3xl p-2">
                <img src="./icons/food.png" alt="Еда" class="pl-8" />
                <h2 class="text-white text-lg pl-2 mt-2">Еда</h2>
            </div>
        </a>
        <a href="./doctor.php">
            <div class="bg-linear-to-r from-[#D9D9D9] to-[#737373] rounded-3xl p-2">
                <img src="./icons/doctor.png" alt="Доктор" class="pl-8" />
                <h2 class="text-white text-lg pl-2 mt-2">Доктор</h2>
            </div>
        </a>
    </div>

    <div class="mt-10 flex gap-6 px-6 justify-center">
        <div>
            <img src="<?= $photo ?>" alt="Фото пользователя" class="user-photo mx-auto" />
            <h3 class="text-2xl font-medium text-center mt-3"><?= $name ?></h3>
        </div>
        <div>
            <div class="bg-[#F2F2F2] rounded-2xl py-4 px-6">
                <h3 class="text-xl font-bold text-center mb-4">Ваша<br> мед.карта</h3>
                <a href="" target="_blank" rel="noopener" id="openModalBtn" class="bg-white rounded-2xl py-2 px-4 font-medium block text-center">
                    Подробнее
                </a>
            </div>
            <a href="profile.php" class="bg-[#EAF0EC] rounded-3xl py-2 font-medium text-center block mx-auto mt-4">
                Профиль
            </a>
        </div>
    </div>


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


</header>

<main>
    <section class="px-6">
        <div class="w-full h-18 bg-linear-to-r from-[#FC575B] to-[#8F51FC] rounded-4xl mx-auto mt-6">
            <h4 class="text-white text-2xl text-center py-4">Категории здоровья</h4>
        </div>
    </section>

    <section class="mt-8 bg-white px-6 rounded-t-4xl pt-5 pb-26">
        <a href="metrics.php">
            <div class="flex border-b border-[#E0E0E0] items-center gap-4 py-4">
                <img src="./icons/health-category_1.png" alt="Метрические данные" class="w-12 h-12" />
                <h5 class="text-xl font-medium">Метрические данные</h5>
            </div>
        </a>

        <a href="water.php">
            <div class="flex border-b border-[#E0E0E0] items-center gap-4 py-4">
                <img src="./icons/health-category_2.png" alt="Вода" class="w-12 h-12" />
                <h5 class="text-xl font-medium">Вода</h5>
            </div>
        </a>

        <a href="heart.php">
            <div class="flex border-b border-[#E0E0E0] items-center gap-4 py-4">
                <img src="./icons/health-category_3.png" alt="Сердце" class="w-12 h-12" />
                <h5 class="text-xl font-medium">Сердце</h5>
            </div>
        </a>

        <a href="cycle.php">
            <div class="flex items-center gap-4 py-4">
                <img src="./icons/health-category_4.png" alt="Дневник цикла" class="w-12 h-12" />
                <h5 class="text-xl font-medium">Дневник цикла</h5>
            </div>
        </a>
    </section>
</main>

<div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex items-center justify-center gap-8 py-4 px-6 rounded-3xl bg-[#E1DFE4]/50 w-[50%] max-w-[300px] min-w-[200px]">
    <a href="account.php"><img src="./icons/nav-el-home-active.png" alt="Главная"></a>
    <a href="services.php"><img src="./icons/nav-el-services-noactive.png" alt="Сервисы"></a>
    <a href="help.php"><img src="./icons/nav-el-help-noactive.png" alt="Помощь"></a>
</div>


<div id="notifModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-xl w-11/12 max-h-[80vh] overflow-y-auto p-6 relative shadow-lg">
        <button id="closeNotifModal" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-3xl font-bold" aria-label="Закрыть">&times;</button>
        <h2 class="text-2xl font-bold mb-4 text-center">Ваши уведомления</h2>

        <?php if (empty($veterinaryRequests) && empty($doctorAppointments)): ?>
            <p class="text-center text-gray-600">Пользователь пока не осуществлял никаких заявок или уведомлений нет</p>
        <?php else: ?>
            <div class="space-y-6">
                <?php if (!empty($veterinaryRequests)): ?>
                    <section>
                        <h3 class="text-xl font-semibold mb-2 border-b border-gray-300 pb-1">Заявки к ветеринару</h3>
                        <ul class="space-y-3 max-h-60 overflow-y-auto">
                            <?php foreach ($veterinaryRequests as $req): ?>
                                <li class="border border-gray-200 rounded p-3 bg-green-50 shadow-sm">
                                    <p><strong>Животное:</strong> <?=htmlspecialchars($req['animal'])?> <?=htmlspecialchars($req['breed'])?></p>
                                    <p><strong>Дата услуги:</strong> <?=htmlspecialchars($req['service_date'])?></p>
                                    <p><strong>Место встречи:</strong> <?=htmlspecialchars($req['meeting_place'])?></p>
                                    <?php if ($req['wishes']): ?>
                                        <p><strong>Пожелания:</strong> <?=nl2br(htmlspecialchars($req['wishes']))?></p>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-500">Создано: <?=htmlspecialchars($req['created_at'])?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <?php if (!empty($doctorAppointments)): ?>
                    <section>
                        <h3 class="text-xl font-semibold mb-2 border-b border-gray-300 pb-1">Записи к врачу</h3>
                        <ul class="space-y-3 max-h-60 overflow-y-auto">
                            <?php foreach ($doctorAppointments as $app): ?>
                                <li class="border border-gray-200 rounded p-3 bg-blue-50 shadow-sm">
                                    <p><strong>Пациент:</strong> <?=htmlspecialchars($app['patient_name'])?></p>
                                    <p><strong>Телефон:</strong> <?=htmlspecialchars($app['phone'])?></p>
                                    <p><strong>Дата приёма:</strong> <?=htmlspecialchars($app['appointment_date'])?> в <?=htmlspecialchars($app['appointment_time'])?></p>
                                    <p class="text-sm text-gray-500">Создано: <?=htmlspecialchars($app['created_at'])?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
   
    const bell = document.getElementById('bell');
    const notifModal = document.getElementById('notifModal');
    const closeBtn = document.getElementById('closeNotifModal');

    bell.addEventListener('click', function(e) {
        e.preventDefault();
        notifModal.classList.remove('hidden');
        notifModal.classList.add('flex');
    });

    closeBtn.addEventListener('click', () => {
        notifModal.classList.add('hidden');
        notifModal.classList.remove('flex');
    });

    notifModal.addEventListener('click', (e) => {
        if (e.target === notifModal) {
            notifModal.classList.add('hidden');
            notifModal.classList.remove('flex');
        }
    });
</script>

    <script>
                const openModalBtns = document.querySelectorAll('#openModalBtn');
                const closeModalBtn = document.getElementById('closeModalBtn');
                const modal = document.getElementById('modal');

                openModalBtns.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault(); 
                        modal.classList.remove('hidden');
                    });
                });

                closeModalBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });

                // Закрыть по клику вне окна
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            </script>

                <script>
                    
        document.addEventListener('DOMContentLoaded', () => {
        const searchIcon = document.getElementById('searchIcon');
        const searchContainer = document.getElementById('searchContainer');
        const siteSearchInput = document.getElementById('siteSearch');

     
        const routes = {
            'метрические данные': 'metrics.php',
            'метрика': 'metrics.php',
            'вода': 'water.php',
            'сердце': 'heart.php',
            'дневник цикла': 'cycle.php',
            'месячные': 'cycle.php',
            'аптека': 'pharmacy.php',
            'еда': 'feed.php',
            'питание': 'feed.php',
            'доктор': 'doctor.php',
            'профиль': 'profile.php',
            'запись к врачу': 'doctor.php',
            'запись к ветеринару': 'veterinary.php',
            'ветеринар': 'veterinary.php',
        };

      
        searchIcon.addEventListener('click', (e) => {
            e.preventDefault();
            if (searchContainer.classList.contains('hidden')) {
            searchContainer.classList.remove('hidden');
            siteSearchInput.focus();
            } else {
            searchContainer.classList.add('hidden');
            siteSearchInput.value = '';
            }
        });

     
        siteSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
            e.preventDefault();
            const query = siteSearchInput.value.trim().toLowerCase();

            if (!query) return;

            let foundRoute = null;
            for (const key in routes) {
                if (query.includes(key)) {
                foundRoute = routes[key];
                break;
                }
            }

            if (foundRoute) {
                window.location.href = foundRoute;
            } else {
                alert('По вашему запросу ничего не найдено.');
            }
            }
        });
        });

        </script>

</body>
</html>
