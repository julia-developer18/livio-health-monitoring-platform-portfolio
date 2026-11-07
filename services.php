<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />


  <link rel="icon" href="./icons/livio-main-logo.png" type="image/x-icon">

    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />

    <title>livio</title>
</head>
<body class="font-['Roboto'] bg-[#F2F3F8] ">
  <header class="px-6">
    <div class="flex items-center justify-between mt-20">
      <h1 class=" font-bold text-4xl ">Услуги</h1>
      <button
        type="submit"
        id="myButton"
        class="bg-[#6B6B6B] text-white py-2 px-4 rounded-2xl text-xl"
      >
        Подписка
      </button>
    </div>
    <p class="text-[#A7A7A7] mt-6">В этом разделе объединены все сервисы для взаимодейтсвия с приложением.</p>

    <div class="max-w-md mx-auto mt-6">
      <label for="search" class="relative block">

        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-500">

          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1 0 6 13.5a7.5 7.5 0 0 0 10.65 3.15z"
            />
          </svg>
        </span>
        <input
          id="search"
          type="text"
          placeholder="Поиск услуги"
          class="w-full pl-12 pr-4 py-3 rounded-xl bg-[#D9D9D9] placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#BD363A] focus:border-transparent font-['Roboto']"
        />
      </label>
    </div>
  </header>

  <main>
    <section class="px-6">
      <h2 class=" text-3xl font-bold py-6">Врач дома</h2>

      <div class="flex gap-2 justify-center items-center">

        <a href="#" class="openModalBtn">
          <div class="bg-linear-to-r from-[#ACABAB] to-[#5F5B5B] rounded-3xl p-4">
            <img src="images/service-terapevt.png" alt="" />
          </div>
          <h3 class="text-sm mt-2 font-light text-center">Терапевт</h3>
        </a>
        <a href="veterinary.php">
          <div class="bg-linear-to-r from-[#007F3C] to-[#036E5B] rounded-3xl p-4">
            <img src="images/service-veterinaria.png" alt="" />
          </div>
          <h3 class="text-sm mt-2 font-light text-center">Ветеринар</h3>
        </a>
        <a href="#" class="openModalBtn">
          <div class="bg-linear-to-r from-[#172250] to-[#5D0B79]  rounded-3xl p-4">
            <img src="images/service-analiz.png" alt="" />
          </div>
          <h3 class=" text-sm mt-2 text-center">Анализы</h3>
        </a>
      </div>
    </section>

    <section class="px-6 pb-26">
      <h2 class=" text-3xl font-bold py-6">Сервисы</h2>

      <div class="flex gap-2 justify-center items-center">
        <a href="pharmacy.php">
          <div class="bg-linear-to-r from-[#C93133] to-[#9D557A] rounded-3xl p-4">
            <img src="images/service-apteka.png" alt="" />
          </div>
          <h3 class="text-sm mt-2 font-light text-center">Аптека</h3>
        </a>
        <a href="feed.php">
          <div class="bg-linear-to-r from-[#E98769] to-[#962746] rounded-3xl p-4">
            <img src="images/service-food.png" alt="" />
          </div>
          <h3 class="text-sm mt-2 font-light text-center">Питание</h3>
        </a>
  
        <a href="#" class="openModalBtn">
          <div class="bg-linear-to-r from-[#7691A9] to-[#575B76]  rounded-3xl p-4">
            <img src="images/service-strahovanie.png" alt="" />
          </div>
          <h3 class=" text-sm mt-2 text-center">Страхование</h3>
        </a>
      </div>
    </section>
  </main>


  <div
    id="modal"
    class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center hidden z-50"
  >
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-4/5 p-6 relative">

      <button
        id="closeModalBtn"
        aria-label="Закрыть"
        class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 transition text-2xl font-bold"
      >
        &times;
      </button>

      <p class="text-center text-gray-800 text-lg font-semibold">
        Данная функция находится в разработке
      </p>
    </div>
  </div>

    <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex items-center justify-center gap-8 py-4 px-6 rounded-3xl bg-[#E1DFE4]/50 w-[50%] max-w-[300px] min-w-[200px]">
        <a href="account.php"><img src="./icons/nav-el-home-active.png" alt="Главная"></a>
        <a href="services.php"><img src="./icons/nav-el-services-noactive.png" alt="Сервисы"></a>
        <a href="help.php"><img src="./icons/nav-el-help-noactive.png" alt="Помощь"></a>
    </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const button = document.getElementById('myButton');

      button.addEventListener('click', () => {
        if (button.textContent === 'Вы подписаны') {
          button.textContent = 'Подписка';
          button.style.backgroundColor = '#6B6B6B';
          button.style.color = 'white';
        } else {
          button.textContent = 'Вы подписаны';
          button.style.backgroundColor = '#C41A14';
          button.style.color = 'white';
        }
      });


      const openModalBtns = document.querySelectorAll('.openModalBtn');
      const modal = document.getElementById('modal');
      const closeModalBtn = document.getElementById('closeModalBtn');

      openModalBtns.forEach((btn) => {
        btn.addEventListener('click', (event) => {
          event.preventDefault(); 
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

 
      const searchInput = document.getElementById('search');

      searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
      
        const serviceLinks = document.querySelectorAll('main section div.flex a');

        serviceLinks.forEach((link) => {
          const text = link.querySelector('h3').textContent.toLowerCase();
        
          if (text.includes(query)) {
            link.style.display = '';
          } else {
            link.style.display = 'none';
          }
        });
      });
    });
  </script>
  <script src="js/error.js"></script>
</body>
</html>