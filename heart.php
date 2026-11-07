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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

$errors = [];
$success = '';


if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM heart_exams WHERE id = ? AND user_id = ?");
        $stmt->execute([$delete_id, $_SESSION['user_id']]);
    }
   
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $exam_date = $_POST['exam_date'] ?? '';
    $ecg = trim($_POST['ecg'] ?? '');
    $echo = trim($_POST['echo'] ?? '');
    $mri = trim($_POST['mri'] ?? '');
    $ct_angio = trim($_POST['ct_angio'] ?? '');

    // Валидация даты
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $exam_date)) {
        $errors[] = "Введите корректную дату обследования (ГГГГ-ММ-ДД).";
    }

    if (!$errors) {
        if ($id > 0) {
           
            $stmt = $pdo->prepare("UPDATE heart_exams SET exam_date = ?, ecg = ?, echo = ?, mri = ?, ct_angio = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$exam_date, $ecg ?: null, $echo ?: null, $mri ?: null, $ct_angio ?: null, $id, $_SESSION['user_id']]);
            $success = "Данные обследования успешно обновлены.";
        } else {
           
            $stmt = $pdo->prepare("INSERT INTO heart_exams (user_id, exam_date, ecg, echo, mri, ct_angio) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $exam_date, $ecg ?: null, $echo ?: null, $mri ?: null, $ct_angio ?: null]);
            $success = "Данные обследования успешно добавлены.";
        }
    }
}


$stmt = $pdo->prepare("SELECT * FROM heart_exams WHERE user_id = ? ORDER BY exam_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<title>Сердце - результаты обследований</title>
<style>
  
    .exam-item {
        position: relative;
    }
    .delete-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #ec5252;
        background: transparent;
        border: none;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
        
    }
    .delete-btn:hover {
        color: #ba3a3a;
    }
</style>
</head>
<body class="bg-linear-to-r from-[#F35454] to-[#7E2F2F] font-['Roboto'] py-4">

<header class="px-6">
    <a href="account.php" class="absolute top-6 left-6"><img src="/icons/arrow-white.png" alt=""></a>
    <img src="/icons/category-heart-icon.png" alt="" class="absolute top-10 right-6">
    <h1 class="font-bold text-[25px] mt-30 text-white">Результаты обследований сердца</h1>
    <p class="mt-2 text-white text-sm">Добавляйте и просматривайте результаты ЭКГ, УЗИ, МРТ и КТ-ангиографии</p>
</header>

<main class="mt-6 bg-[#FFF1F1] rounded-4xl py-10">


    <div class="space-y-6 px-6">
        <?php if (count($exams) === 0): ?>
            <p class="text-center text-[#959595] py-8">Нет данных обследований</p>
        <?php else: ?>
            <?php foreach ($exams as $exam): ?>
            <div class="rounded-2xl p-6 bg-white shadow exam-item">
                <div class="flex justify-between items-center mb-2">
                    <div class="font-medium text-[#4D4D4D]"><?= htmlspecialchars($exam['exam_date']) ?></div>
                    <button
                        class="editBtn text-[#EC5252] hover:underline"
                        data-id="<?= $exam['id'] ?>"
                        data-exam_date="<?= htmlspecialchars($exam['exam_date']) ?>"
                        data-ecg="<?= htmlspecialchars($exam['ecg'] ?? '') ?>"
                        data-echo="<?= htmlspecialchars($exam['echo'] ?? '') ?>"
                        data-mri="<?= htmlspecialchars($exam['mri'] ?? '') ?>"
                        data-ct_angio="<?= htmlspecialchars($exam['ct_angio'] ?? '') ?>"
                    >Редактировать</button>
                </div>
                <div class="space-y-2 text-[#6B6B6B] font-light">
                    <div><span class="font-normal">ЭКГ:</span> <?= nl2br(htmlspecialchars($exam['ecg'] ?? '-')) ?></div>
                    <div><span class="font-normal">УЗИ сердца (ЭхоКГ):</span> <?= nl2br(htmlspecialchars($exam['echo'] ?? '-')) ?></div>
                    <div><span class="font-normal">МРТ сердца:</span> <?= nl2br(htmlspecialchars($exam['mri'] ?? '-')) ?></div>
                    <div><span class="font-normal">КТ-ангиография:</span> <?= nl2br(htmlspecialchars($exam['ct_angio'] ?? '-')) ?></div>
                </div>
                <button 
                    class="delete-btn" 
                    onclick="if(confirm('Вы уверены, что хотите удалить эту запись?')) { window.location='?delete_id=<?= $exam['id'] ?>'; }" 
                    aria-label="Удалить запись"
                    type="button"
                >
             <img src="/icons/krest.png" alt="" class="w-4 h-4">
            </button>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button id="addExamBtn" class="mt-10 bg-[#7E2F2F] text-white py-4 px-8 rounded-2xl hover:bg-[#BA3A3A] transition block mx-auto ">Добавить обследование</button>
</main>


<div id="examModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50 px-3">
    <div class="bg-white rounded-3xl shadow-lg w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <button id="closeModalBtn" aria-label="Закрыть" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 transition text-2xl font-bold">&times;</button>
        <h2 id="modalTitle" class="text-2xl font-semibold mb-6 text-center text-[#975656] mt-6">Добавить обследование</h2>

        <form id="examForm" method="post" class="space-y-4">
            <input type="hidden" name="id" id="examId" value="">

            <div>
                <label for="exam_date" class="block font-medium mb-1 text-[#4D4D4D]">Дата обследования *</label>
                <input type="date" id="exam_date" name="exam_date" required class="w-full border border-[#C75656] rounded-xl py-4 px-4 text-[#6B6B6B]" />
            </div>

            <div>
                <label for="ecg" class="block font-medium mb-1 text-[#4D4D4D]">ЭКГ</label>
                <textarea id="ecg" name="ecg" rows="3" placeholder="Введите результаты или комментарии" class="w-full border border-[#C75656] rounded-xl py-4 px-4 text-[#6B6B6B] resize-y"></textarea>
            </div>

            <div>
                <label for="echo" class="block font-medium mb-1 text-[#4D4D4D]">УЗИ сердца (ЭхоКГ)</label>
                <textarea id="echo" name="echo" rows="3" placeholder="Введите результаты или комментарии" class="w-full border border-[#C75656] rounded-xl py-4 px-4 text-[#6B6B6B] resize-y"></textarea>
            </div>

            <div>
                <label for="mri" class="block font-medium mb-1 text-[#4D4D4D]">МРТ сердца</label>
                <textarea id="mri" name="mri" rows="3" placeholder="Введите результаты или комментарии" class="w-full border border-[#C75656] rounded-xl py-4 px-4 text-[#6B6B6B] resize-y"></textarea>
            </div>

            <div>
                <label for="ct_angio" class="block font-medium mb-1 text-[#4D4D4D]">КТ-ангиография</label>
                <textarea id="ct_angio" name="ct_angio" rows="3" placeholder="Введите результаты или комментарии" class="w-full border border-[#C75656] rounded-xl py-4 px-4 text-[#6B6B6B] resize-y"></textarea>
            </div>

            <button type="submit" class="w-full bg-[#7E2F2F] hover:bg-[#BA3A3A] text-white py-3 rounded-2xl font-semibold transition">
                Сохранить
            </button>
        </form>
    </div>
</div>

<script>
const addExamBtn = document.getElementById('addExamBtn');
const examModal = document.getElementById('examModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const modalTitle = document.getElementById('modalTitle');
const examForm = document.getElementById('examForm');

const examIdInput = document.getElementById('examId');
const examDateInput = document.getElementById('exam_date');
const ecgInput = document.getElementById('ecg');
const echoInput = document.getElementById('echo');
const mriInput = document.getElementById('mri');
const ctAngioInput = document.getElementById('ct_angio');

function clearForm() {
    examForm.reset();
    examIdInput.value = '';
}


addExamBtn.addEventListener('click', () => {
    modalTitle.textContent = 'Добавить обследование';
    clearForm();
    examModal.classList.remove('hidden');
});


closeModalBtn.addEventListener('click', () => {
    examModal.classList.add('hidden');
    clearForm();
});


window.addEventListener('click', e => {
    if (e.target === examModal) {
        examModal.classList.add('hidden');
        clearForm();
    }
});


document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        modalTitle.textContent = 'Редактировать обследование';
        clearForm();
        examModal.classList.remove('hidden');

        examIdInput.value = btn.getAttribute('data-id');
        examDateInput.value = btn.getAttribute('data-exam_date') || '';
        ecgInput.value = btn.getAttribute('data-ecg') || '';
        echoInput.value = btn.getAttribute('data-echo') || '';
        mriInput.value = btn.getAttribute('data-mri') || '';
        ctAngioInput.value = btn.getAttribute('data-ct_angio') || '';
    });
});
</script>

</body>
</html>