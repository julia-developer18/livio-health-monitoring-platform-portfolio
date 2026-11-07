 const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modal = document.getElementById('modal');

    openModalBtn.addEventListener('click', () => {
      modal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    // Дополнительно: закрыть по клику вне окна
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });