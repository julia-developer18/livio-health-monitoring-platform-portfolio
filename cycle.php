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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['date'])) {
    header('Content-Type: application/json');
    $date = $_POST['date'];


    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Некорректный формат даты']);
        exit;
    }

    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO cycle_diary (user_id, date) VALUES (?, ?)");
        $stmt->execute([$user_id, $date]);
        echo json_encode(['success' => true, 'action' => 'add', 'date' => $date]);
        exit;
    } elseif ($_POST['action'] === 'remove') {
        $stmt = $pdo->prepare("DELETE FROM cycle_diary WHERE user_id = ? AND date = ?");
        $stmt->execute([$user_id, $date]);
        echo json_encode(['success' => true, 'action' => 'remove', 'date' => $date]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Неизвестное действие']);
        exit;
    }
}


$stmt = $pdo->prepare("SELECT date FROM cycle_diary WHERE user_id = ?");
$stmt->execute([$user_id]);
$markedDates = $stmt->fetchAll(PDO::FETCH_COLUMN);
$markedDatesJson = json_encode($markedDates);


function groupPeriods(array $dates): array {
    sort($dates);
    $periods = [];
    $start = null;
    $prev = null;
    foreach ($dates as $date) {
        if ($start === null) {
            $start = $date;
            $prev = $date;
            continue;
        }
        $prevDate = new DateTime($prev);
        $currDate = new DateTime($date);
        $diff = $prevDate->diff($currDate)->days;
        if ($diff === 1) {
            $prev = $date;
        } else {
            $periods[] = ['start' => $start, 'end' => $prev, 'length' => $prevDate->diff(new DateTime($start))->days + 1];
            $start = $date;
            $prev = $date;
        }
    }
    if ($start !== null) {
        $periods[] = ['start' => $start, 'end' => $prev, 'length' => (new DateTime($start))->diff(new DateTime($prev))->days + 1];
    }
    return $periods;
}

$periods = groupPeriods($markedDates);
?>

<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Дневник цикла</title>
          <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
      <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="icon" href="./icons/livio-main-logo.png" type="image/x-icon">

        <style>
        .day-cell {
            @apply w-10 h-10 flex items-center justify-center rounded-full cursor-pointer select-none border border-gray-300 transition-colors duration-200 ease-in-out;
        }
        .marked > span {
            color: #BD363A; 
            font-weight: 700;
        }
        .day-cell:hover:not(.marked) {
            @apply bg-pink-300;
        }
        </style>
    </head>
<body class="bg-linear-to-r from-[#FF8CC6] to-[#A23465] font-['Roboto']">

    <header class="px-6">
        <a href="account.php" class="absolute top-6 left-6"><img src="/icons/arrow-white.png" alt=""></a>
            <img src="./icons/category-cicle-icon.png" alt="" class="absolute top-12 right-6">
            <h1 class=" font-bold text-[white] text-[28px] mt-35 text-[#4D4D4D]">Дневник цикла</h1>
        <p class=" text-white font-light font-lg">Отмечайте дни менструального цикла в календаре</p>
    </header>

<main class="max-w-md w-full select-none bg-white rounded-4xl shadow py-8 mt-4">

 
    <div class="flex justify-center items-center gap-4 mb-4 select-none">
        <button id="prevMonth" class="bg-[#FB88C1] hover:bg-[#FF68B3] active:bg-[#FFC2E0] transition  rounded-2xl text-white font-light py-2 px-6">
            Назад
        </button>
        <div id="currentMonthYear" class="text-lg font-semibold"></div>
        <button id="nextMonth" class="bg-[#A43668] hover:bg-[#FF68B3] active:bg-[#850740] transition  rounded-2xl text-white font-light py-2 px-6 ">
           Вперед
        </button>
    </div>

    <div id="calendar" class="grid grid-cols-7 gap-2 text-center text-gray-700 font-medium px-4">
        
        <div>Пн</div><div>Вт</div><div>Ср</div><div>Чт</div><div>Пт</div><div>Сб</div><div>Вс</div>
    </div>


<div class="max-w-md w-full mt-6 flex justify-center">
    <button id="openPeriodsBtn" class="bg-[#A43668] text-white py-4 px-8 rounded-2xl hover:bg-pink-700 transition font-semibold">
        Периоды менструального цикла
    </button>
</div>


<div id="periodsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg max-w-md w-11/12 p-8 relative max-h-[80vh] overflow-y-auto">
        <button id="closePeriodsModal" aria-label="Закрыть" 
            class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-3xl font-bold">&times;</button>
        <h2 class="text-2xl font-semibold mb-4 text-center text-[#850740]">Периоды менструального цикла</h2>
        <?php if (count($periods) === 0): ?>
            <p class="text-center text-gray-500">Периоды не зафиксированы</p>
        <?php else: ?>
            <table class="w-full text-center border border-white rounded-2xl">
                <thead class="bg-[#A43668] text-white">
                    <tr>
                        <th class="p-2 border-r border-white">Начало</th>
                        <th class="p-2 border-r border-white">Конец</th>
                        <th class="p-2">Длит.(дней)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($periods as $p): ?>
                        <tr class="border-t border-white bg-[#B2557F] text-white">
                            <td class="p-2 border-r border-white w-25"><?= htmlspecialchars($p['start']) ?></td>
                            <td class="p-2 border-r border-white w-25"><?= htmlspecialchars($p['end']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($p['length']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>


</main>

<script>


let storedYear = localStorage.getItem('calendarYear');
let storedMonth = localStorage.getItem('calendarMonth');

let year = storedYear !== null ? parseInt(storedYear) : (new Date()).getFullYear();
let month = storedMonth !== null ? parseInt(storedMonth) : (new Date()).getMonth();

localStorage.removeItem('calendarYear');
localStorage.removeItem('calendarMonth');

const markedDates = new Set(<?= $markedDatesJson ?>);
const calendar = document.getElementById('calendar');
const currentMonthYear = document.getElementById('currentMonthYear');
const prevMonthBtn = document.getElementById('prevMonth');
const nextMonthBtn = document.getElementById('nextMonth');

function monthName(m) {
    const monthNames = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
    return monthNames[m];
}

function updateMonthYear() {
    currentMonthYear.textContent = monthName(month) + ' ' + year;
}

function generateCalendar(year, month) {
    updateMonthYear();


    while (calendar.children.length > 7) {
        calendar.removeChild(calendar.lastChild);
    }

    const firstDate = new Date(year, month, 1);
    let startDay = firstDate.getDay();
    startDay = startDay === 0 ? 7 : startDay; // воскресенье = 7 для сдвига

    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 1; i < startDay; i++) {
        const emptyCell = document.createElement('div');
        calendar.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('div');
        cell.className = 'day-cell';

        const dayStr = String(day).padStart(2, '0');
        const monthStr = String(month + 1).padStart(2, '0');
        const dateStr = `${year}-${monthStr}-${dayStr}`;

        cell.innerHTML = `<span>${day}</span>`;

        if (markedDates.has(dateStr)) {
            cell.classList.add('marked');
        }

        cell.addEventListener('click', () => {
            if (markedDates.has(dateStr)) {
                updateDay(dateStr, false, cell);
            } else {
                updateDay(dateStr, true, cell);
            }
        });

        calendar.appendChild(cell);
    }
}

function updateDay(dateStr, add, cell) {
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: add ? 'add' : 'remove',
            date: dateStr
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (add) {
                markedDates.add(dateStr);
                cell.classList.add('marked');
            } else {
                markedDates.delete(dateStr);
                cell.classList.remove('marked');
            }
          
            localStorage.setItem('calendarYear', year);
            localStorage.setItem('calendarMonth', month);
            location.reload();
        } else {
            alert('Ошибка: ' + (data.error || 'неизвестная ошибка'));
        }
    })
    .catch(() => {
        alert('Ошибка сети');
    });
}

prevMonthBtn.addEventListener('click', () => {
    month--;
    if (month < 0) {
        month = 11;
        year--;
    }
    generateCalendar(year, month);
});

nextMonthBtn.addEventListener('click', () => {
    month++;
    if (month > 11) {
        month = 0;
        year++;
    }
    generateCalendar(year, month);
});

generateCalendar(year, month);


const openPeriodsBtn = document.getElementById('openPeriodsBtn');
const periodsModal = document.getElementById('periodsModal');
const closePeriodsModal = document.getElementById('closePeriodsModal');


openPeriodsBtn.addEventListener('click', () => {
    periodsModal.classList.remove('hidden');
    periodsModal.classList.add('flex');
});


closePeriodsModal.addEventListener('click', () => {
    periodsModal.classList.add('hidden');
    periodsModal.classList.remove('flex');
});


periodsModal.addEventListener('click', (e) => {
    if (e.target === periodsModal) {
        periodsModal.classList.add('hidden');
        periodsModal.classList.remove('flex');
    }
});

</script>

</body>
</html>