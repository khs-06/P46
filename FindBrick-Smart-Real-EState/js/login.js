// --- Helper Validation Functions ---
function validateEmail(email) {
  return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
}
function validatePassword(password) {
  // 8+ chars, upper, lower, digit, special char
  return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password);
}
function validatePhone(phone) {
  // Only digits, 10 in length
  return /^[0-9]{10}$/.test(phone);
}


// --- Login Form Validation ---
$('#loginForm').on('submit', function(e) {
  let valid = true;
  let email = $('#email').val().trim();
  let password = $('#password').val();
  let emailErr = '', passErr = '';

  if (!email) {
    emailErr = "Please enter your email.";
    valid = false;
  } else if (!validateEmail(email)) {
    emailErr = "Please enter a valid email address.";
    valid = false;
  }
  if (!password) {
    passErr = "Please enter your password.";
    valid = false;
  } else if (!validatePassword(password)) {
    passErr = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character.";
    valid = false;
  }

  $('#emailError').text(emailErr);
  $('#passwordError').text(passErr);

  if (!valid) e.preventDefault();
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

// --- Register Form Validation ---
$('#registerForm').on('submit', function(e) {
  let valid = true;
  let name = $('#name').val().trim();
  let userType = $('#user_type').val();
  let phone = $('#phone').val().trim();
  let email = $('#email').val().trim();
  let password = $('#password').val();
  let confirm_password = $('#confirm_password').val();
  let image = $('#image').val();

  let nameErr = '', userTypeErr = '', phoneErr = '', emailErr = '', passErr = '', confirmErr = '', imageErr = '';

  if (!name) {
    nameErr = "Please enter your name.";
    valid = false;
  }
  if (!userType) {
    userTypeErr = "Please select account type.";
    valid = false;
  }
  if (!phone) {
    phoneErr = "Please enter your phone number.";
    valid = false;
  } else if (!validatePhone(phone)) {
    phoneErr = "Please enter a valid phone number (digits only, 10 digits).";
    valid = false;
  }
  if (!email) {
    emailErr = "Please enter your email.";
    valid = false;
  } else if (!validateEmail(email)) {
    emailErr = "Please enter a valid email address.";
    valid = false;
  }
  if (!password) {
    passErr = "Please enter a password.";
    valid = false;
  } else if (!validatePassword(password)) {
    passErr = "Password must be at least 8 characters, including uppercase, lowercase, number, and special character.";
    valid = false;
  }
  if (!confirm_password) {
    confirmErr = "Please confirm your password.";
    valid = false;
  } else if (password !== confirm_password) {
    confirmErr = "Passwords do not match.";
    valid = false;
  }
  if (!image) {
    imageErr = "Please select a profile image.";
    valid = false;
  }

  $('#nameError').text(nameErr);
  $('#userTypeError').text(userTypeErr);
  $('#phoneError').text(phoneErr);
  $('#emailError').text(emailErr);
  $('#passwordError').text(passErr);
  $('#confirmPasswordError').text(confirmErr);
  $('#imageError').text(imageErr);

  if (!valid) e.preventDefault();
});

// --- Password show/hide toggle (for both forms) ---
$('#togglePassword').on('click', function() {
  var password = $('#password');
  var type = password.attr('type') === 'password' ? 'text' : 'password';
  password.attr('type', type);
  $(this).find('span').toggleClass('fa-eye fa-eye-slash');
});

// --- Only allow digits on phone input ---
$('#phone').on('keypress', function (e) {
  // Allow: backspace, delete, arrows, tab
  if ($.inArray(e.keyCode, [8, 9, 37, 39, 46]) !== -1) return;
  // Only digits (0-9)
  if (e.which < 48 || e.which > 57) e.preventDefault();
});


// --- Only alphabet and spaces for name input ---
$('#name').on('input', function() {
  let clean = $(this).val().replace(/[^a-zA-Z\s]/g, '');
  if ($(this).val() !== clean) $(this).val(clean);
});