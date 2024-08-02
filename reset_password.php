<?php
require 'config.php';  // فایل تنظیمات و اتصال به پایگاه داده را وارد کنید

// چک کردن اینکه آیا توکن در URL موجود است
if (!isset($_GET['token']) || empty($_GET['token'])) {
    echo "Invalid request!";
    exit;
}

$token = trim($_GET['token']);

// چک کردن وجود توکن در پایگاه داده
$stmt = $mysqli->prepare("SELECT id FROM users WHERE token = ? AND token_created_at > NOW() - INTERVAL 1 HOUR");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    echo "Invalid or expired token!";
    $stmt->close();
    exit;
}

$stmt->close();

// پردازش فرم ارسال رمز عبور جدید
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = trim($_POST['new_password']);
    
    if (empty($new_password)) {
        echo "Password is required!";
    } else {
        // هش کردن رمز عبور جدید
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // به‌روزرسانی رمز عبور و پاک کردن توکن
        $stmt = $mysqli->prepare("UPDATE users SET password = ?, token = NULL, token_created_at = NULL WHERE token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);
        if ($stmt->execute()) {
            echo "Password has been reset successfully!";
        } else {
            echo "Failed to reset password!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Reset Password</h2>
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-600">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
