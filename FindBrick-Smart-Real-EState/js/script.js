// Section fade-in animation on scroll
document.addEventListener('DOMContentLoaded', function() {
    const fadeElements = document.querySelectorAll('.gallery-card, .service-card, .about-card, .testimonial-card, .footer-card');
    const fadeInOnScroll = () => {
        fadeElements.forEach(el => {
            const rect = el.getBoundingClientRect();
            if(rect.top < window.innerHeight - 100) {
                el.style.opacity = 1;
                el.style.transform = "translateY(0)";
            }
        });
    };
    fadeElements.forEach(el => {
        el.style.opacity = 0;
        el.style.transform = "translateY(30px)";
        el.style.transition = "all 0.8s";
    });
    window.addEventListener('scroll', fadeInOnScroll);
    fadeInOnScroll();
});
// Header background on scroll
window.addEventListener('scroll', function () {
  const header = document.getElementById('mainHeader');
  if (header) {
    if (window.scrollY > 10) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  }
});
// Mobile nav toggle
const mobileNavBtn = document.getElementById('mobileNavBtn');
const navList = document.getElementById('navbarNavList');
if (mobileNavBtn && navList) {
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
}
// Footer current year
document.addEventListener('DOMContentLoaded', function () {
  const yearEl = document.getElementById('year');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }
  // Smooth scroll to top for "Back to top" links
  document.querySelectorAll('a[href="#"]').forEach(link => {
    if (link.textContent.trim().toLowerCase() === "back to top") {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  });
});
// Dropdown hover for desktop
$(function() {
  // Only for screens >= 992px (desktop)
  if (window.matchMedia('(min-width: 992px)').matches) {
    $('.navbar .dropdown').hover(
      function() {
        $(this).addClass('show');
        $(this).find('.dropdown-menu').addClass('show');
        $(this).find('.dropdown-toggle').attr('aria-expanded', 'true');
      },
      function() {
        $(this).removeClass('show');
        $(this).find('.dropdown-menu').removeClass('show');
        $(this).find('.dropdown-toggle').attr('aria-expanded', 'false');
      }
    );
  }
});

// Search form action (simulate search redirect)
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');
  const searchContainer = document.getElementById('searchContainer');
  if (searchInput && searchBtn && searchContainer) {
    searchBtn.addEventListener('click', function (e) {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query) {
        window.location.href = `search.php?query=${encodeURIComponent(query)}`;
      } else {
        alert('Please enter a search term.');
      }
    });
  }
});
// Bootstrap Carousel auto-play
$('.carousel').carousel({
  interval: 3500,
  pause: "hover"
});


function clearError(event, error) {
  if (event) {
    event.addEventListener("input", function() {
      error.textContent = "";
    });
  }
}

clearError(document.getElementById('email'), document.getElementById('emailError'));
clearError(document.getElementById('password'), document.getElementById('passwordError'));
clearError(document.getElementById('name'), document.getElementById('nameError'));
clearError(document.getElementById('user_type'), document.getElementById('userTypeError'));
clearError(document.getElementById('phone'), document.getElementById('phoneError'));
clearError(document.getElementById('confirm_password'), document.getElementById('confirmPasswordError'));
clearError(document.getElementById('image'), document.getElementById('imageError'));
clearError(document.getElementById('old_password'), document.getElementById('oldPasswordError'));
// Email validation
// function validateEmail(email) {
//   const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//   return re.test(String(email).toLowerCase());
// }
// // Password validation (min 8 chars, uppercase, lowercase, number, special char)
// function validatePassword(password) {
//   const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
//   return re.test(password);
// }
// // Phone validation (10 digits)
// function validatePhone(phone) {
//   const re = /^\d{10}$/;
//   return re.test(phone);
// }

// dark mode toggle
// const darkModeToggle = document.getElementById('darkModeToggle');
// if (darkModeToggle) {
//   darkModeToggle.addEventListener('click', function () {
//     document.body.classList.toggle('dark-mode');
//   });
// }
// const checkbox = document.getElementById("checkbox")
// checkbox.addEventListener("change", () => {
//   document.body.classList.toggle("dark")
// })