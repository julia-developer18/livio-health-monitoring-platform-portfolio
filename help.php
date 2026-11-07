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


    <title>Контакты и помощь</title>
</head>
<body class="font-['Roboto']">
    <header>
        <a href="./profile.php">
            <img src="icons/profile-icon.png" alt="" class="absolute top-12 right-6" />
        </a>
        <a href="doctor.php">
            <div class="bg-[#F5F5F7] flex mt-35 py-6 px-4 mx-6 rounded-3xl justify-between items-center">
                <div>
                    <img src="./images/help-img.png" alt="" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-right">Записаться на<br /> прием к доктору</h1>
                    <p class="text-[#979797] text-right text-[12px] mt-2 w-53">Нажмите сюда для того, чтобы<br /> записаться к врачу в вашей клинике</p>
                </div>
            </div>
        </a>
    </header>
    <main>
        <section class="pb-26">
            <div id="modal" class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-lg max-w-sm w-4/5 p-6 relative">
                    <!-- Крестик закрытия -->
                    <button id="closeModalBtn" aria-label="Закрыть" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 transition text-2xl font-bold">
                        &times;
                    </button>
                    <p class="text-center text-gray-800 text-lg font-semibold">
                        Данная функция находится в разработке
                    </p>
                </div>
            </div>
            <div class="px-9 mt-10">
                <!-- Заменил id на class="openModalBtn" -->
                <a href="#" class="openModalBtn">
                    <div class="flex items-center gap-4 py-4 justify-between">
                        <div class="flex gap-6 items-center">
                            <img src="icons/help-el-1.png" alt="" />
                            <h5 class="text-2xl ">Специалист</h5>
                        </div>
                        <div>
                            <img src="icons/arrow-right.png" alt="" class="w-6 h-6" />
                        </div>
                    </div>
                </a>
                <a href="#" class="openModalBtn">
                    <div class="flex items-center gap-4 py-4 justify-between">
                        <div class="flex gap-6 items-center">
                            <img src="icons/help-el-2.png" alt="" />
                            <h5 class="text-2xl ">Справочная</h5>
                        </div>
                        <div>
                            <img src="icons/arrow-right.png" alt="" class="w-6 h-6" />
                        </div>
                    </div>
                </a>
                <a href="#" class="openModalBtn">
                    <div class="flex items-center gap-4 py-4 justify-between">
                        <div class="flex gap-6 items-center">
                            <img src="icons/help-el-3.png" alt="" />
                            <h5 class="text-2xl ">Колл-центр</h5>
                        </div>
                        <div>
                            <img src="icons/arrow-right.png" alt="" class="w-6 h-6" />
                        </div>
                    </div>
                </a>
                <a href="#" class="openModalBtn">
                    <div class="flex items-center gap-4 py-4 justify-between">
                        <div class="flex gap-6 items-center">
                            <img src="icons/help-el-4.png" alt="" />
                            <h5 class="text-2xl ">Поликлиника</h5>
                        </div>
                        <div>
                            <img src="icons/arrow-right.png" alt="" class="w-6 h-6" />
                        </div>
                    </div>
                </a>
                <a href="#" class="openModalBtn">
                    <div class="flex items-center gap-4 py-4 justify-between">
                        <div class="flex gap-6 items-center">
                            <img src="icons/help-el-5.png" alt="" />
                            <h5 class="text-2xl ">Найти больницу</h5>
                        </div>
                        <div>
                            <img src="icons/arrow-right.png" alt="" class="w-6 h-6" />
                        </div>
                    </div>
                </a>
                <a href="#" class="openModalBtn">
                    <div class="flex items-center gap-4 py-4 justify-between">
                        <div class="flex gap-6 items-center">
                            <img src="icons/help-el-6.png" alt="" />
                            <h5 class="text-2xl ">Спам</h5>
                        </div>
                        <div>
                            <img src="icons/arrow-right.png" alt="" class="w-6 h-6" />
                        </div>
                    </div>
                </a>
            </div>
        </section>
    </main>

    <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex items-center justify-center gap-8 py-4 px-6 rounded-3xl bg-[#E1DFE4]/50 w-[50%] max-w-[300px] min-w-[200px]">
        <a href="account.php"><img src="./icons/nav-el-home-active.png" alt="Главная"></a>
        <a href="services.php"><img src="./icons/nav-el-services-noactive.png" alt="Сервисы"></a>
        <a href="help.php"><img src="./icons/nav-el-help-noactive.png" alt="Помощь"></a>
    </div>
    
    <script>
        const openModalBtns = document.querySelectorAll('.openModalBtn');
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

      
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>