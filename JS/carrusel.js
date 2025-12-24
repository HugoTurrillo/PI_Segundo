const track = document.querySelector('.carousel-track');
const dots = document.querySelectorAll('.dot');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let index = 0;

function updateCarousel() {
  const offset = index * -600; // desplazamiento exacto por imagen
  track.style.transform = `translateX(${offset}px)`;

  dots.forEach(dot => dot.classList.remove('active'));
  dots[index].classList.add('active');
}

nextBtn.addEventListener('click', () => {
  index = (index + 1) % dots.length;
  updateCarousel();
});

prevBtn.addEventListener('click', () => {
  index = (index - 1 + dots.length) % dots.length;
  updateCarousel();
});

dots.forEach((dot, i) => {
  dot.addEventListener('click', () => {
    index = i;
    updateCarousel();
  });
});

updateCarousel();