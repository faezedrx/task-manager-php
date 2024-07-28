<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bamboo</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Google Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Caveat&family=Lobster&display=swap" rel="stylesheet"> -->
    <!-- Favicon -->
    <link rel="icon" href="SERVICE-B.png" type="image/x-icon">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Background Animation -->
        <div id="background" class="absolute inset-0 flex items-center justify-center">
            
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
            <div class="bamboo"></div>
        </div>
        
        <div class="container z-10 flex items-center">
        
            <div class="button-container">
                <a href="#" id="login-btn" class="button">Login</a>
                <a href="#" id="signup-btn" class="button">Sign Up</a>
                <!-- <a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a>                <a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a><a href="#" ></a> -->
                <!-- <a href="ebook/"  class="button" target="_blank">ebook</a> -->
                <!-- <a href="http://faezedrx-portfolio.streamlit.app/" class="button" target="_blank">my portfolio</a> -->
            </div>
            
            <div id="login-form" class="form hidden">
                <div class="form-content">
                    <form action="login.php" method="POST">
                        <h2 class="text-3xl font-bold mb-4">Login</h2>
                        <input type="email" name="username" placeholder="Email" class="form-input mb-4" required>
                        <input type="password" name="password" placeholder="Password" class="form-input mb-4" required>
                        <button type="submit" class="button bg-green-500 hover:bg-green-600">Login</button>
                        <button type="button" id="close-login" class="mt-4 text-gray-500 hover:text-gray-700">Close</button>
                    </form>
                </div>
            </div>
            <div id="signup-form" class="form hidden">
                <div class="form-content">
                    <form action="register.php" method="POST">
                        <h2 class="text-3xl font-bold mb-4">Sign Up</h2>
                        <input type="text" name="nickname" placeholder="Nickname" class="form-input mb-4" required>
                        <input type="email" name="username" placeholder="Email" class="form-input mb-4" required>
                        <input type="password" name="password" placeholder="Password" class="form-input mb-4" required>
                        <div id="password-rules" class="text-gray-500 mb-4">
                            <ul>
                                <li id="rule-length">At least 8 characters long</li>
                                <li id="rule-uppercase">At least one uppercase letter</li>
                                <li id="rule-number">At least one number</li>
                                <li id="rule-special">At least one special character</li>
                            </ul>
                        </div>
                        <button type="submit" class="button bg-blue-500 hover:bg-blue-600">Sign Up</button>
                        <button type="button" id="close-signup" class="mt-4 text-gray-500 hover:text-gray-700">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Custom JS -->
    <script src="script.js"></script>
    <script>
        // Check password validity
        function checkPasswordValidity(password) {
            const rules = {
                length: /.{8,}/,
                uppercase: /[A-Z]/,
                number: /[0-9]/,
                special: /[!@#$%^&*(),.?":{}|<>]/
            };

            document.getElementById('rule-length').style.color = rules.length.test(password) ? 'green' : 'red';
            document.getElementById('rule-uppercase').style.color = rules.uppercase.test(password) ? 'green' : 'red';
            document.getElementById('rule-number').style.color = rules.number.test(password) ? 'green' : 'red';
            document.getElementById('rule-special').style.color = rules.special.test(password) ? 'green' : 'red';
        }

        document.querySelector('#signup-form input[name="password"]').addEventListener('input', function () {
            checkPasswordValidity(this.value);
        });

        // ارسال فرم ثبت‌نام
        document.querySelector('#signup-form form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred!', 'error');
            });
        });

        // ارسال فرم ورود
        document.querySelector('#login-form form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success');
                    window.location.href = data.redirect;
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred!', 'error');
            });
        });
    </script>
        <?php if (isset($_GET['verified']) && $_GET['verified'] == 1): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Verified',
                text: 'Your email has been successfully verified.',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
</body>
</html>
