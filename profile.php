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


$stmt = $pdo->prepare("SELECT name, photo, clinic, oms_photo FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$name = htmlspecialchars($user['name'] ?? '');
$photo = htmlspecialchars($user['photo'] ?? 'icons/default-user.png');
$clinic = $user['clinic'] ? htmlspecialchars($user['clinic']) : '';
$oms_photo = $user['oms_photo'] ? htmlspecialchars($user['oms_photo']) : '';

$errors = [];
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if (isset($_POST['change_name'])) {
        $newName = trim($_POST['new_name'] ?? '');
        if ($newName === '' || !preg_match("/^[а-яА-ЯёЁa-zA-Z\s]+$/u", $newName)) {
            $errors[] = "Введите корректное имя (только буквы).";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$newName, $_SESSION['user_id']]);
            $_SESSION['user_name'] = $newName;
            $name = htmlspecialchars($newName);
            $success = "Имя успешно обновлено.";
        }
    }
    // Изменение фото
    if (isset($_POST['change_photo'])) {
        if (isset($_FILES['new_photo']) && $_FILES['new_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg','image/png','image/gif'];
            if (!in_array($_FILES['new_photo']['type'], $allowedTypes)) {
                $errors[] = "Фото должно быть форматом JPEG, PNG или GIF.";
            } else {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($_FILES['new_photo']['name'], PATHINFO_EXTENSION);
                $photoName = uniqid('photo_', true) . '.' . $ext;
                $photoPath = $uploadDir . $photoName;
                if (move_uploaded_file($_FILES['new_photo']['tmp_name'], $photoPath)) {
                    $dbPhotoPath = 'uploads/' . $photoName;
                    $stmt = $pdo->prepare("UPDATE users SET photo = ? WHERE id = ?");
                    $stmt->execute([$dbPhotoPath, $_SESSION['user_id']]);
                    $_SESSION['user_photo'] = $dbPhotoPath;
                    $photo = htmlspecialchars($dbPhotoPath);
                    $success = "Фото успешно обновлено.";
                } else {
                    $errors[] = "Ошибка загрузки файла.";
                }
            }
        } else {
            $errors[] = "Выберите фото для загрузки.";
        }
    }

    if (isset($_POST['clinic_submit'])) {
        $newClinic = trim($_POST['clinic_name'] ?? '');
        if ($newClinic === '') {
            $errors[] = "Введите название клиники.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET clinic = ? WHERE id = ?");
            $stmt->execute([$newClinic, $_SESSION['user_id']]);
            $clinic = htmlspecialchars($newClinic);
            $success = "Клиника успешно обновлена.";
        }
    }
 
    if (isset($_POST['oms_submit'])) {
        if (isset($_FILES['oms_photo']) && $_FILES['oms_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg','image/png','image/gif'];
            if (!in_array($_FILES['oms_photo']['type'], $allowedTypes)) {
                $errors[] = "Фото полиса должно быть форматом JPEG, PNG или GIF.";
            } else {
                $uploadDir = __DIR__ . '/uploads/oms/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($_FILES['oms_photo']['name'], PATHINFO_EXTENSION);
                $photoName = uniqid('oms_', true) . '.' . $ext;
                $photoPath = $uploadDir . $photoName;
                if (move_uploaded_file($_FILES['oms_photo']['tmp_name'], $photoPath)) {
                    $dbPhotoPath = 'uploads/oms/' . $photoName;
                    $stmt = $pdo->prepare("UPDATE users SET oms_photo = ? WHERE id = ?");
                    $stmt->execute([$dbPhotoPath, $_SESSION['user_id']]);
                    $oms_photo = htmlspecialchars($dbPhotoPath);
                    $success = "Фото полиса успешно обновлено.";
                } else {
                    $errors[] = "Ошибка загрузки фото полиса.";
                }
            }
        } else {
            $errors[] = "Выберите фото полиса для загрузки.";
        }
    }
}
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

<link rel="icon" href="./icons/livio-main-logo.png" type="image/x-icon">



<title>Профиль</title>
<style>
  .user-photo {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
  }
</style>
</head>
<body class="font-['Roboto'] bg-[#EBF0EC] min-h-screen flex flex-col items-center ">

<header class="w-full max-w-md mb-10 relative">
    <h1 class="font-bold text-4xl mb-8 pl-8 pt-15">Профиль</h1>
    <div class="relative"> 
        <div class="bg-white rounded-3xl w-full h-70 absolute inset-0 z-0 top-20">
          
        </div>
        <div class="relative z-10"> <!-- важный момент: z-10, чтобы быть поверх белого квадрата -->
            <img src="<?= $photo ?>" alt="Фото пользователя" class="user-photo mx-auto bg-gray-300" />
            <h2 class="font-bold text-3xl mt-4 text-center"><?= $name ?></h2>
            <p class="text-center text-[#A2A2A2] mt-2">Настройка профиля и периферии</p>

            <div class="mt-8 flex justify-center gap-4">
                <button id="openPhotoModal" class="bg-[#A6A6A6] hover:bg-gray-700 transition text-white py-4 px-3 rounded-3xl">
                    Изменить фото
                </button>
                <button id="openNameModal" class="bg-[#A6A6A6] hover:bg-gray-700 transition text-white py-4 px-3 rounded-3xl">
                    Изменить имя
                </button>
            </div>
        </div>
        
    </div>
</header>

<main class="w-full max-w-md bg-white rounded-3xl mt-10 pb-10 px-6">
   <section class="mt-6">
      <a href="#" id="clinicLink">
         <div class="flex items-center justify-start gap-6 border-b border-gray-200 py-6">
            <img src="icons/profile-icon-clinic.png" alt="">
            <h4 class="text-2xl font-medium">Клиника</h4>
         </div>
      </a>

      <a href="#" id="omsLink">
         <div class="flex items-center justify-start gap-6 border-b border-gray-200 py-6">
            <img src="icons/profile-icon-polis.png" alt="">
            <h4 class="text-2xl font-medium">Полис ОМС</h4>
         </div>
      </a>

      <a href="#" class="openModalBtn">
      <div class="flex items-center justify-start gap-6 py-6">
         <img src="icons/profile-icon-med_karta.png" alt="">
         <h4 class="text-2xl font-medium">Мед.карта</h4>
      </div>
      </a>

   
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
</main>
   <div class="w-full max-w-md px-6 mt-8 mb-6 flex justify-center">
   <form method="post" action="logout.php">
      <button type="submit" class="bg-[#A6A6A6] hover:bg-red-700 text-white font-semibold rounded-3xl py-3 px-10 transition-shadow shadow-md hover:shadow-lg">
         Выйти из аккаунта
      </button>
   </form>
   </div>


<div id="photoModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
   <div class="bg-white rounded-lg shadow-lg max-w-sm w-4/5 p-6 relative">
      <button id="closePhotoModal" aria-label="Закрыть" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 transition text-2xl font-bold">
         &times;
      </button>
      <h3 class="text-center text-gray-800 text-lg font-semibold mb-4">Изменить фото</h3>
      <form method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
         <input type="file" name="new_photo" accept="image/*" required class="border rounded p-2" />
         <button type="submit" name="change_photo" class="bg-[#A6A6A6] hover:bg-[#757575]transition text-white py-2 px-4 rounded-3xl text-lg">
            Сохранить
         </button>
      </form>
   </div>
</div>

<div id="nameModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
   <div class="bg-white rounded-lg shadow-lg max-w-sm w-4/5 p-6 relative">
      <button id="closeNameModal" aria-label="Закрыть" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 transition text-2xl font-bold">
         &times;
      </button>
      <h3 class="text-center text-gray-800 text-lg font-semibold mb-4">Изменить имя</h3>
      <form method="post" class="flex flex-col gap-4">
         <input type="text" name="new_name" required pattern="[а-яА-ЯёЁa-zA-Z\s]+" title="Только буквы" placeholder="Новое имя" value="<?= $name ?>" class="border rounded p-2" />
         <button type="submit" name="change_name" class="bg-[#A6A6A6] hover:bg-[#757575] transition text-white py-2 px-4 rounded-3xl text-lg">
            Сохранить
         </button>
      </form>
   </div>
</div>


<div id="clinicModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
   <div class="bg-white rounded-lg p-6 max-w-sm w-4/5 relative">
      <button id="clinicClose" class="absolute top-3 right-3 text-gray-600 text-2xl font-bold hover:text-black">&times;</button>
      <h3 class="text-xl font-semibold mb-4">Клиника</h3>
      <form method="POST" class="flex flex-col gap-4">
         <input type="text" name="clinic_name" value="<?= $clinic ?>" required class="border rounded px-3 py-2" />
         <button type="submit" name="clinic_submit" class="bg-[#A6A6A6] text-white py-2 rounded hover:bg-[#757575] transition">Сохранить</button>
      </form>
   </div>
</div>


<div id="omsModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
   <div class="bg-white rounded-lg p-6 max-w-sm w-4/5 relative">
      <button id="omsClose" class="absolute top-3 right-3 text-gray-600 text-2xl font-bold hover:text-black">&times;</button>
      <h3 class="text-xl font-semibold mb-4">Полис ОМС</h3>
      <?php if ($oms_photo): ?>
        <img src="<?= $oms_photo ?>" alt="Полис ОМС" class="mb-4 max-w-full rounded shadow" />
      <?php endif; ?>
      <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
        <input type="file" name="oms_photo" accept="image/*" required />
        <button type="submit" name="oms_submit" class="bg-[#A6A6A6] text-white py-2 rounded hover:bg-[#757575] transition">Загрузить</button>
      </form>
   </div>
</div>

<script>
  const openPhotoBtn = document.getElementById('openPhotoModal');
  const closePhotoBtn = document.getElementById('closePhotoModal');
  const photoModal = document.getElementById('photoModal');

  const openNameBtn = document.getElementById('openNameModal');
  const closeNameBtn = document.getElementById('closeNameModal');
  const nameModal = document.getElementById('nameModal');

  const clinicLink = document.getElementById('clinicLink');
  const clinicModal = document.getElementById('clinicModal');
  const clinicClose = document.getElementById('clinicClose');

  const omsLink = document.getElementById('omsLink');
  const omsModal = document.getElementById('omsModal');
  const omsClose = document.getElementById('omsClose');

  openPhotoBtn.addEventListener('click', () => {
    photoModal.classList.remove('hidden');
  });

  closePhotoBtn.addEventListener('click', () => {
    photoModal.classList.add('hidden');
  });

  openNameBtn.addEventListener('click', () => {
    nameModal.classList.remove('hidden');
  });

  closeNameBtn.addEventListener('click', () => {
    nameModal.classList.add('hidden');
  });

  clinicLink.addEventListener('click', e => {
    e.preventDefault();
    clinicModal.classList.remove('hidden');
  });

  clinicClose.addEventListener('click', () => {
    clinicModal.classList.add('hidden');
  });

  omsLink.addEventListener('click', e => {
    e.preventDefault();
    omsModal.classList.remove('hidden');
  });

  omsClose.addEventListener('click', () => {
    omsModal.classList.add('hidden');
  });


  [photoModal, nameModal, clinicModal, omsModal].forEach(modal => {
    modal.addEventListener('click', e => {
      if (e.target === modal) modal.classList.add('hidden');
    });
  });
</script>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    const openModalBtns = document.querySelectorAll('.openModalBtn');
    const modal = document.getElementById('modal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    openModalBtns.forEach(btn => {
      btn.addEventListener('click', event => {
        event.preventDefault(); 
        modal.classList.remove('hidden');
      });
    });

    closeModalBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    modal.addEventListener('click', (event) => {
 
      if (event.target === modal) {
        modal.classList.add('hidden');
      }
    });
  });
</script>

</body>
</html>