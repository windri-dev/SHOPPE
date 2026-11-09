/**
 * PUTRI.J - Hero Carousel Slider
 * Smooth transitions, autoplay with pause on hover, pagination dots & touch swipe.
 */

class HeroSlider {
  constructor(containerId = 'heroSlider') {
    this.container = document.getElementById(containerId);
    if (!this.container) return;

    this.slides = this.container.querySelectorAll('.hero-slide');
    this.dots = this.container.querySelectorAll('.slider-dot');
    this.prevBtn = this.container.querySelector('.slider-arrow.prev');
    this.nextBtn = this.container.querySelector('.slider-arrow.next');

    this.currentIndex = 0;
    this.totalSlides = this.slides.length;
    this.autoplayInterval = null;
    this.autoplayDelay = 5000;

    // Touch swipe coordinates
    this.touchStartX = 0;
    this.touchEndX = 0;

    this.init();
  }

  init() {
    if (this.totalSlides <= 1) return;

    // Dot click events
    this.dots.forEach((dot, index) => {
      dot.addEventListener('click', (e) => {
        e.preventDefault();
        this.goToSlide(index);
        this.resetAutoplay();
      });
    });

    // Arrow button events
    if (this.prevBtn) {
      this.prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.prevSlide();
        this.resetAutoplay();
      });
    }

    if (this.nextBtn) {
      this.nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.nextSlide();
        this.resetAutoplay();
      });
    }

    // Pause on hover
    this.container.addEventListener('mouseenter', () => this.stopAutoplay());
    this.container.addEventListener('mouseleave', () => this.startAutoplay());

    // Mobile touch swipe handling
    this.container.addEventListener('touchstart', (e) => {
      this.touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    this.container.addEventListener('touchend', (e) => {
      this.touchEndX = e.changedTouches[0].screenX;
      this.handleSwipe();
    }, { passive: true });

    // Keyboard navigation when focused
    this.container.setAttribute('tabindex', '0');
    this.container.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') {
        this.prevSlide();
        this.resetAutoplay();
      } else if (e.key === 'ArrowRight') {
        this.nextSlide();
        this.resetAutoplay();
      }
    });

    this.startAutoplay();
  }

  goToSlide(index) {
    if (this.totalSlides <= 1) return;

    const newIndex = ((index % this.totalSlides) + this.totalSlides) % this.totalSlides;
    if (newIndex === this.currentIndex) return;

    if (this.slides[this.currentIndex]) {
      this.slides[this.currentIndex].classList.remove('active');
    }
    if (this.dots[this.currentIndex]) {
      this.dots[this.currentIndex].classList.remove('active');
    }

    this.currentIndex = newIndex;

    if (this.slides[this.currentIndex]) {
      this.slides[this.currentIndex].classList.add('active');
    }
    if (this.dots[this.currentIndex]) {
      this.dots[this.currentIndex].classList.add('active');
    }
  }

  nextSlide() {
    this.goToSlide(this.currentIndex + 1);
  }

  prevSlide() {
    this.goToSlide(this.currentIndex - 1);
  }

  handleSwipe() {
    const swipeThreshold = 40;
    const diff = this.touchStartX - this.touchEndX;

    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) {
        this.nextSlide(); // Swiped left -> next
      } else {
        this.prevSlide(); // Swiped right -> prev
      }
      this.resetAutoplay();
    }
  }

  startAutoplay() {
    this.stopAutoplay();
    if (this.totalSlides > 1) {
      this.autoplayInterval = setInterval(() => {
        this.nextSlide();
      }, this.autoplayDelay);
    }
  }

  stopAutoplay() {
    if (this.autoplayInterval) {
      clearInterval(this.autoplayInterval);
      this.autoplayInterval = null;
    }
  }

  resetAutoplay() {
    this.stopAutoplay();
    this.startAutoplay();
  }
}

function initHeroSlider() {
  const container = document.getElementById('heroSlider');
  if (container) {
    window.heroSlider = new HeroSlider('heroSlider');
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initHeroSlider);
} else {
  initHeroSlider();
}

export default HeroSlider;
