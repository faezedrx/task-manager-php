<?php
require 'config.php';
require 'dashboard-styles.php';
require 'sidebar.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$error_message = '';
$success_message = '';

if (isset($_GET['telegram_id'])) {
    $telegram_id = $_GET['telegram_id'];
    if (!empty($telegram_id) && isset($_SESSION['username'])) {
        $db = Database::getInstance();
        $mysqli = $db->getConnection();
        $username = $_SESSION['username'];

        $stmt = $mysqli->prepare("UPDATE users SET telegram_id = ? WHERE username = ?");
        $stmt->bind_param('ss', $telegram_id, $username);
        if ($stmt->execute()) {
            $success_message = "Telegram ID updated successfully.";
        } else {
            $error_message = "Error updating Telegram ID.";
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['username'])) {
        $error_message = "User not logged in.";
    } else {
        $db = Database::getInstance();
        $mysqli = $db->getConnection();

        $username = $_SESSION['username'];
        $stmt = $mysqli->prepare("SELECT password, nickname FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password, $current_nickname);
        $stmt->fetch();
        $stmt->close();

        if (isset($_POST['update_nickname'])) {
            $new_nickname = $_POST['new_nickname'];
            if (!empty($new_nickname)) {
                $stmt = $mysqli->prepare("UPDATE users SET nickname = ? WHERE username = ?");
                $stmt->bind_param('ss', $new_nickname, $username);
                if ($stmt->execute()) {
                    $_SESSION['nickname'] = $new_nickname;
                    $success_message = "Nickname updated successfully.";
                } else {
                    $error_message = "Error updating nickname.";
                }
                $stmt->close();
            }
        }

        if (isset($_POST['update_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            if (!empty($current_password) && password_verify($current_password, $hashed_password)) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE username = ?");
                $stmt->bind_param('ss', $new_hashed_password, $username);
                if ($stmt->execute()) {
                    $success_message = "Password updated successfully.";
                } else {
                    $error_message = "Error updating password.";
                }
                $stmt->close();
            } elseif (!empty($current_password)) {
                $error_message = "Current password is incorrect.";
            }
        }

        if (isset($_POST['update_telegram_id'])) {
            $telegram_id = $_POST['telegram_id'];
            if (!empty($telegram_id)) {
                $stmt = $mysqli->prepare("UPDATE users SET telegram_id = ? WHERE username = ?");
                $stmt->bind_param('ss', $telegram_id, $username);
                if ($stmt->execute()) {
                    $success_message = "Telegram ID updated successfully.";
                } else {
                    $error_message = "Error updating Telegram ID.";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .content {
            overflow: auto;
            flex: 1;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>
<body>
<div class="content flex-1 p-8 bg-gradient-to-r from-green-400 via-green-500 to-green-600">
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

    <!-- فرم‌ها در دو ردیف و دو ستون -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- فرم تغییر نام مستعار -->
        <form action="home.php" method="post" class="bg-white p-8 rounded-xl shadow-2xl space-y-6">
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">Update Your Nickname</h2>
            <div>
                </br></br>
                <label for="new_nickname" class="block text-gray-700 text-sm font-medium mb-2">New Nickname</label>
                <input type="text" id="new_nickname" name="new_nickname" class="w-full border border-gray-300 rounded-lg p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500 transition duration-200 ease-in-out" placeholder="Enter new nickname">
                </br></br>
            </div>
            <button type="submit" name="update_nickname" class="w-full bg-gradient-to-r from-blue-500 to-teal-500 text-white p-4 rounded-lg shadow-lg transform transition-transform hover:scale-105 hover:shadow-xl">
                Update Nickname
            </button>
        </form>

        <!-- فرم تغییر رمز عبور -->
        <form action="home.php" method="post" class="bg-white p-8 rounded-xl shadow-2xl space-y-6">
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">Update Your Password</h2>
            <div>
                <label for="current_password" class="block text-gray-700 text-sm font-medium mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="w-full border border-gray-300 rounded-lg p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500 transition duration-200 ease-in-out" placeholder="Enter current password">
            </div>
            <div>
                <label for="new_password" class="block text-gray-700 text-sm font-medium mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" class="w-full border border-gray-300 rounded-lg p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500 transition duration-200 ease-in-out" placeholder="Enter new password">
            </div>
            <button type="submit" name="update_password" class="w-full bg-gradient-to-r from-blue-500 to-teal-500 text-white p-4 rounded-lg shadow-lg transform transition-transform hover:scale-105 hover:shadow-xl">
                Update Password
            </button>
        </form>

        <!-- فرم تغییر Telegram ID -->
        <form action="home.php" method="post" class="bg-white p-8 rounded-xl shadow-2xl space-y-6">
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">Update Your Telegram ID</h2>
            <div>
                <label for="telegram_id" class="block text-gray-700 text-sm font-medium mb-2">Telegram ID</label>
                <input type="text" id="telegram_id" name="telegram_id" class="w-full border border-gray-300 rounded-lg p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500 transition duration-200 ease-in-out" placeholder="Enter your Telegram ID">
            </div>
            <button type="submit" name="update_telegram_id" class="w-full bg-gradient-to-r from-blue-500 to-teal-500 text-white p-4 rounded-lg shadow-lg transform transition-transform hover:scale-105 hover:shadow-xl">
                Update Telegram ID
            </button>
        </form>

        <!-- دکمه ربات تلگرام -->
        <div class="bg-white p-8 rounded-xl shadow-2xl space-y-6 text-center flex flex-col items-center justify-center">
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">Connect to Telegram</h2>
            <a href="https://t.me/bamboosbot?start=<?php echo urlencode($_SESSION['username']); ?>" class="inline-block bg-blue-500 text-white p-4 rounded-lg shadow-lg transform transition-transform hover:scale-105 hover:shadow-xl">
                Connect to Telegram
            </a>
        </div>
    </div>
</div>
</body>
</html>
