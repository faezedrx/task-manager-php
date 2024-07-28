document.addEventListener('DOMContentLoaded', function() {
    const loginBtn = document.getElementById('login-btn');
    const signupBtn = document.getElementById('signup-btn');
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const closeLogin = document.getElementById('close-login');
    const closeSignup = document.getElementById('close-signup');

    loginBtn.addEventListener('click', function(event) {
        event.preventDefault();
        loginForm.classList.remove('hidden');
        signupForm.classList.add('hidden');
    });

    signupBtn.addEventListener('click', function(event) {
        event.preventDefault();
        signupForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
    });

    closeLogin.addEventListener('click', function() {
        loginForm.classList.add('hidden');
    });

    closeSignup.addEventListener('click', function() {
        signupForm.classList.add('hidden');
    });

});

