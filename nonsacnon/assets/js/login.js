(function () {
  'use strict';
  var form = document.getElementById('loginForm');
  var password = document.getElementById('password');
  var toggle = document.querySelector('.password-toggle');
  var forgot = document.querySelector('.forgot-link');
  var forgotMessage = document.getElementById('forgotMessage');

  if (toggle && password) {
    toggle.addEventListener('click', function () {
      var visible = password.type === 'text';
      password.type = visible ? 'password' : 'text';
      toggle.setAttribute('aria-pressed', String(!visible));
      toggle.setAttribute('aria-label', visible ? 'Hiện mật khẩu' : 'Ẩn mật khẩu');
    });
  }
  if (forgot && forgotMessage) {
    forgot.addEventListener('click', function () {
      var expanded = forgot.getAttribute('aria-expanded') === 'true';
      forgot.setAttribute('aria-expanded', String(!expanded));
      forgotMessage.hidden = expanded;
    });
  }
  if (form) {
    form.addEventListener('submit', function () {
      var submit = form.querySelector('.login-submit');
      if (submit) {
        submit.disabled = true;
        submit.setAttribute('aria-busy', 'true');
      }
    });
  }
  if (document.body.getAttribute('data-demo') === 'true') {
    document.querySelectorAll('.demo-buttons button').forEach(function (button) {
      button.addEventListener('click', function () {
        document.getElementById('email').value = button.getAttribute('data-email') || '';
        document.getElementById('password').value = 'demo-password';
        document.getElementById('email').focus();
      });
    });
    document.querySelector('.demo-area').removeAttribute('aria-hidden');
  }
}());
