<?php
require 'dashboard-styles.php'; // برای بارگذاری استایل‌ها و اسکریپت‌ها
require 'sidebar.php'; // برای بارگذاری سایدبار
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
// متغیرهای خطا و موفقیت
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // بررسی وجود username در جلسه
    if (!isset($_SESSION['username'])) {
        $error_message = "User not logged in.";
    } else {
        require 'config.php';

        // گرفتن اطلاعات فرم
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $new_nickname = $_POST['new_nickname'];

        // دریافت اطلاعات کاربر از پایگاه داده
        $username = $_SESSION['username'];
        $stmt = $mysqli->prepare("SELECT password, nickname FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password, $current_nickname);
        $stmt->fetch();
        $stmt->close();

        $update_password = false;
        if (!empty($current_password) && password_verify($current_password, $hashed_password)) {
            // به‌روزرسانی رمز عبور جدید
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param('ss', $new_hashed_password, $username);
            if ($stmt->execute()) {
                $success_message = "Password updated successfully.";
            } else {
                $error_message = "Error updating password.";
            }
            $stmt->close();
            $update_password = true;
        } elseif (!empty($current_password)) {
            $error_message = "Current password is incorrect.";
        }

        if (!empty($new_nickname) && !$update_password) {
            // به‌روزرسانی نام مستعار جدید
            $stmt = $mysqli->prepare("UPDATE users SET nickname = ? WHERE username = ?");
            $stmt->bind_param('ss', $new_nickname, $username);
            if ($stmt->execute()) {
                $_SESSION['nickname'] = $new_nickname; // بروزرسانی نام مستعار در جلسه
                $success_message = "Nickname updated successfully.";
            } else {
                $error_message = "Error updating nickname.";
            }
            $stmt->close();
        }

        $mysqli->close();
    }
}
?>

<div class="content flex-1 p-8 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">
    <h1 class="text-4xl font-extrabold text-white mb-4">
        Welcome, <?php echo isset($_SESSION['nickname']) ? htmlspecialchars($_SESSION['nickname']) : 'User'; ?>!
    </h1>
    <p class="text-white text-lg mb-8">This is your dashboard.</p>

    <!-- نمایش پیام‌های خطا و موفقیت -->
    <?php if ($error_message): ?>
        <div class="bg-red-600 text-white p-4 mb-4 rounded-lg shadow-lg ring-2 ring-red-400 transform transition-transform scale-105 hover:scale-110">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="bg-green-600 text-white p-4 mb-4 rounded-lg shadow-lg ring-2 ring-green-400 transform transition-transform scale-105 hover:scale-110">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <!-- فرم تغییر نام مستعار و رمز عبور -->
    <form action="home.php" method="post" class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-2xl space-y-6">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Update Your Information</h2>

        <!-- تغییر نام مستعار -->
        <div>
            <label for="new_nickname" class="block text-gray-700 text-sm font-medium mb-2">New Nickname</label>
            <input type="text" id="new_nickname" name="new_nickname" class="w-full border border-gray-300 rounded-lg p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500 transition duration-200 ease-in-out" placeholder="Enter new nickname">
        </div>

        <!-- تغییر رمز عبور -->
        <div>
            <label for="current_password" class="block text-gray-700 text-sm font-medium mb-2">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="w-full border border-gray-300 rounded-lg p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500 transition duration-200 ease-in-out" placeholder="Enter current password">
        </div>
        <div>
            <label for="new_password" class="block text-gray-700 text-sm font-medium mb-2">New Password</label>
            <input type="password" id="new_password" name="new_password" class="w-full border border-gray-300 rounded-lg p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500 transition duration-200 ease-in-out" placeholder="Enter new password">
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-teal-500 text-white p-4 rounded-lg shadow-lg transform transition-transform hover:scale-105 hover:shadow-xl">
            Update Information
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuButton = document.getElementById('menu-button');
        const sidebar = document.getElementById('sidebar');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    });
</script>
</body>
</html>
