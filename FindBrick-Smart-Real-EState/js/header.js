// Change header background on scroll
window.addEventListener('scroll', function () {
  const header = document.getElementById('mainHeader');
  if (window.scrollY > 10) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});

// Mobile nav toggle
const mobileNavBtn = document.getElementById('mobileNavBtn');
const navList = document.getElementById('navbarNavList');

function closeNavOnOutsideClick(event) {
  if (
    navList.classList.contains('show') &&
    !navList.contains(event.target) &&
    !mobileNavBtn.contains(event.target)
  ) {
    navList.classList.remove('show');
    document.removeEventListener('click', closeNavOnOutsideClick);
  }
}

mobileNavBtn.addEventListener('click', function (e) {
  navList.classList.toggle('show');
  if (navList.classList.contains('show')) {
    document.addEventListener('click', closeNavOnOutsideClick);
  } else {
    document.removeEventListener('click', closeNavOnOutsideClick);
  }
});

window.addEventListener('resize', function () {
  if (window.innerWidth >= 992) {
    navList.classList.remove('show');
    document.removeEventListener('click', closeNavOnOutsideClick);
  }
});