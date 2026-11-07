<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
      <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


    <link rel="icon" href="./icons/livio-main-logo.png" type="image/x-icon">


    <title>Аптека</title>
</head>
<body class="font-['Roboto']  ">
    <header class="bg-linear-to-r from-[#C2373E] to-[#9F5274] px-8 items-center rounded-b-2xl pt-20 pb-4">
        <div class="flex justify-between items-center">
            <h1 class="text-white text-2xl">Аптека</h1>
            <div class="flex gap-2 items-center ">
                <img src="icons/2gis.png" alt="" class="w-[20px] h-[20px]">
                <p class="text-white">Местоположение</p>
            </div>
        </div>
        <div class="mt-2">
            <div class="flex justify-between items-center">
                <div class="">
                       <img src=".icons/pharmacy.png" alt="">
                </div>
                <div class="">
                        <input 
                            id="search-input"
                            type="text" 
                            placeholder="Поиск..." 
                            class="bg-[#F1F0F6] rounded-xl py-2 px-4 w-full max-w-md mx-auto block focus:outline-none focus:ring-2 focus:ring-[#A05173]" 
                        />
                </div>
            </div>
        </div>
    </header>
    <main>


<section class="mt-10 px-4">
    <div class="carousel-wrapper" style="display: flex; gap: 1rem; overflow-x: scroll; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;">
        <style>
        .carousel-wrapper::-webkit-scrollbar {
            display: none;
        }
        .carousel-item {
            cursor: pointer;
        }
        .carousel-item.active {
            border: 2px solid #BF3943;
        }
        </style>
        
        <div class="carousel-item" data-filter="discount" style="flex: 0 0 160px;" onclick="filterCards('discount')">
            <div class="bg-[#FDE7F4] rounded-2xl py-4 px-4">
                <img src="icons/farma_skidka_el.png" alt="" class="block mx-auto">
            </div>
            <h3 class="text-center">Скидки</h3>
        </div>
        <div class="carousel-item" data-filter="medicine" style="flex: 0 0 160px;" onclick="filterCards('medicine')">
            <div class="bg-[#EDFBE4] rounded-2xl py-4 px-6">
                <img src="icons/farma_tablet_el.png" alt="" class="block mx-auto">
            </div>
            <h3 class="text-center">Лекарства</h3>
        </div>
        <div class="carousel-item" data-filter="bad" style="flex: 0 0 160px;" onclick="filterCards('bad')">
            <div class="bg-[#D6F7FC] rounded-2xl py-4 px-6">
                <img src="icons/farma_bad_el.png" alt="" class="block mx-auto">
            </div>
            <h3 class="text-center">Бады</h3>
        </div>
        <div class="carousel-item" data-filter="all" style="flex: 0 0 160px;" onclick="filterCards('all')">
            <div class="bg-[#FFF1E4] rounded-2xl py-4 px-6">
                <img src="icons/apteka_all.png" alt="" class="block mx-auto">
            </div>
            <h3 class="text-center">Все</h3>
        </div>
    </div>
</section>

<section class="mt-4">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <div id="product-grid" class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-4">

           
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="bad" data-discount="true">
                <img src="images/magnii.jpeg" alt="Компливит Магний" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-1">Компливит Магний</h3>
                <div class="text-center mb-2">
                    <span style="text-decoration: line-through; color: gray;">750 руб</span> <span style="color:#BF3943; font-weight:bold;">600 руб</span>
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

           
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="medicine" data-discount="false">
                <img src="images/bistropol.png" alt="Бисопролол" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Бисопролол</h3>
                <div class="text-center mb-2">
                    450 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

        
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="bad" data-discount="true">
                <img src="images/velwomen.jpg" alt="Велвумен" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-1">Велвумен (напиток)</h3>
                <div class="text-center mb-2">
                    <span style="text-decoration: line-through; color: gray;">950 руб</span> <span style="color:#BF3943; font-weight:bold;">830 руб</span>
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

           
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="medicine" data-discount="true">
                <img src="images/metoprolol.png" alt="Метопролол" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-1">Метопролол</h3>
                <div class="text-center mb-2">
                    <span style="text-decoration: line-through; color: gray;">520 руб</span> <span style="color:#BF3943; font-weight:bold;">420 руб</span>
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

            
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="bad" data-discount="false">
                <img src="images/platium.jpeg" alt="Платинум" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Перфектил Платинум</h3>
                <div class="text-center mb-2">
                    1200 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

           
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="medicine" data-discount="true">
                <img src="images/karvefdilol.png" alt="Карведилол" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-1">Карведилол</h3>
                <div class="text-center mb-2">
                    <span style="text-decoration: line-through; color: gray;">850 руб</span> <span style="color:#BF3943; font-weight:bold;">700 руб</span>
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

         
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="bad" data-discount="false">
                <img src="images/pikovit.jpg" alt="Пиковит" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Пиковит Комплекс</h3>
                <div class="text-center mb-2">
                    500 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

           
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="medicine" data-discount="false">
                <img src="images/amlodipin.jpg" alt="Амлодипин" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Амлодипин</h3>
                <div class="text-center mb-2">
                    600 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

          
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="bad" data-discount="false">
                <img src="images/adekta.jpg" alt="Адекта" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Адекта</h3>
                <div class="text-center mb-2">
                    700 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

            
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="medicine" data-discount="false">
                <img src="images/nimopin.jpg" alt="Нимодипин" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Нимодипин</h3>
                <div class="text-center mb-2">
                    500 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

          
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="bad" data-discount="false">
                <img src="images/kids.jpg" alt="Витрум Кидс" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Витрум Кидс Плюс</h3>
                <div class="text-center mb-2">
                    880 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

          
            <div class="bg-white rounded-2xl shadow p-4 flex flex-col items-center" data-type="medicine" data-discount="false">
                <img src="images/nifedipin.jpg" alt="Нифедипин" class="w-full h-28 object-contain rounded-lg mb-3" />
                <h3 class="text-center text-base font-semibold mb-2">Нифедипин</h3>
                <div class="text-center mb-2">
                    480 руб
                </div>
                <a href="#" class="text-[#BF3943] text-sm">Подробнее</a>
            </div>

        </div>
    </div>
</section>



        <section class="px-4 pb-26">
            <div class="bg-linear-to-r from-[#9D557A] to-[#BE3638] text-white px-6 pt-8 pb-4 rounded-2xl">
                <div class="flex gap-2 items-center">
                    <div class="">
                        <img src="images/apteka_ar.png" alt="">
                    </div>
                    <div class="">
                        <h4 class="text-3xl font-medium">Health control</h4>
                        <p class="text-xl font-medium">Семья</p>
                    </div>

                </div>
                <p class="mt-4 text-[14px] font-medium mb-6">Оплачивайте бонусами до 50%  от покупке</p>
                <button type="submit" class="bg-[#FDE7F4] text-[#924C71] rounded-xl py-4 px-8 text-lg font-medium block mx-auto">Привязать</button>
            </div>
        </section>
    </main>
    <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex items-center justify-center gap-8 py-4 px-6 rounded-3xl bg-[#E1DFE4]/50 w-[50%] max-w-[300px] min-w-[200px]">
        <a href="account.php"><img src="./icons/nav-el-home-active.png" alt="Главная"></a>
        <a href="services.php"><img src="./icons/nav-el-services-noactive.png" alt="Сервисы"></a>
        <a href="help.php"><img src="./icons/nav-el-help-noactive.png" alt="Помощь"></a>
    </div>

                <script>
            function filterCards(type) {
                const cards = document.querySelectorAll('#product-grid > div');
                cards.forEach(card => {
                    if(type === 'all') {
                        card.style.display = 'flex';
                    } else if(type === 'discount') {
                        card.style.display = card.dataset.discount === 'true' ? 'flex' : 'none';
                    } else {
                        card.style.display = card.dataset.type === type ? 'flex' : 'none';
                    }
                });
            }
            </script>
                <script>
            const carouselWrapper = document.querySelector('.carousel-wrapper');

           
            let isDown = false;
            let startX;
            let scrollLeft;

            carouselWrapper.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - carouselWrapper.offsetLeft;
                scrollLeft = carouselWrapper.scrollLeft;
                carouselWrapper.style.cursor = 'grabbing';
            });

            carouselWrapper.addEventListener('mouseleave', () => {
                isDown = false;
                carouselWrapper.style.cursor = 'grab';
            });

            carouselWrapper.addEventListener('mouseup', () => {
                isDown = false;
                carouselWrapper.style.cursor = 'grab';
            });

            carouselWrapper.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - carouselWrapper.offsetLeft;
                const walk = (startX - x); 
                carouselWrapper.scrollLeft = scrollLeft + walk;
            });

          
            let touchStartX = 0;
            let touchScrollLeft = 0;

            carouselWrapper.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].pageX;
                touchScrollLeft = carouselWrapper.scrollLeft;
            }, { passive: true });

            carouselWrapper.addEventListener('touchmove', (e) => {
                const touchX = e.touches[0].pageX;
                const walk = touchStartX - touchX;
                carouselWrapper.scrollLeft = touchScrollLeft + walk;
            }, { passive: true });

           
            carouselWrapper.style.cursor = 'grab';
            </script>
            


            <script>
            const searchInput = document.getElementById('search-input');
            const cards = document.querySelectorAll('#product-grid > div');

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim().toLowerCase();

                cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                
                if (title.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
                });
            });
            </script>


</body>
</html>