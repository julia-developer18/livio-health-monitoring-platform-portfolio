const img = document.getElementById('jumpingImg');

    let startTime = null;
    const amplitude = 5; // амплитуда прыжка в пикселях
    const frequency = 0.001; // скорость колебаний (чем меньше, тем медленнее)

    function animateJump(timestamp) {
      if (!startTime) startTime = timestamp;
      const elapsed = timestamp - startTime;

      
      const translateY = amplitude * Math.sin(elapsed * frequency * 2 * Math.PI);

     
      img.style.transform = `translateY(${translateY}px)`;

      
      requestAnimationFrame(animateJump);
    }

    requestAnimationFrame(animateJump);

const bell = document.getElementById('bell');

bell.addEventListener('click', () => {
  bell.classList.remove('shake');
  void bell.offsetWidth; // перезапуск анимации
  bell.classList.add('shake');
});    


