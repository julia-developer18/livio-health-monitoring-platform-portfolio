    const carousel = document.getElementById('carousel');
    const totalItems = carousel.children.length;
    let currentIndex = 0;

    let startX = 0;
    let isDragging = false;

    const carouselWrapper = document.getElementById('carousel-wrapper');
    let containerWidth = carouselWrapper.offsetWidth;

    const stepPercent = 50;

    const maxTranslate = 0;
    const minTranslate = -stepPercent * (totalItems - 2);

    function setTranslateX(percent, animate = true) {
      if (animate) {
        carousel.style.transition = 'transform 0.3s ease-in-out';
      } else {
        carousel.style.transition = 'none';
      }
      carousel.style.transform = `translateX(${percent}%)`;
    }

    function touchStart(e) {
      startX = e.touches[0].clientX;
      isDragging = true;
      carousel.style.transition = 'none';
    }

    function touchMove(e) {
      if (!isDragging) return;

      const currentX = e.touches[0].clientX;
      let deltaX = currentX - startX;

      const deltaPercent = (deltaX / containerWidth) * 100;
      let moveTo = -currentIndex * stepPercent + deltaPercent;

      if (moveTo > maxTranslate) moveTo = maxTranslate;
      if (moveTo < minTranslate) moveTo = minTranslate;

      setTranslateX(moveTo, false);
    }

    function touchEnd(e) {
      if (!isDragging) return;
      isDragging = false;

      const endX = e.changedTouches[0].clientX;
      let deltaX = endX - startX;
      const thresholdPx = 30;

      if (deltaX > thresholdPx && currentIndex > 0) {
        currentIndex--;
      } else if (deltaX < -thresholdPx && currentIndex < totalItems - 2) {
        currentIndex++;
      }

      setTranslateX(-currentIndex * stepPercent, true);
    }

    function updateOnResize() {
      containerWidth = carouselWrapper.offsetWidth;
      setTranslateX(-currentIndex * stepPercent, false);
    }

    setTranslateX(0, true);

    carousel.addEventListener('touchstart', touchStart);
    carousel.addEventListener('touchmove', touchMove);
    carousel.addEventListener('touchend', touchEnd);
    carousel.addEventListener('touchcancel', touchEnd);

    window.addEventListener('resize', updateOnResize);