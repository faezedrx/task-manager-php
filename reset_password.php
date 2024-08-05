<?php
require 'config.php';  
// چک کردن اینکه آیا توکن در URL موجود است
if (!isset($_GET['token']) || empty($_GET['token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request!']);
    exit;
}

$token = trim($_GET['token']);

// دریافت اتصال به پایگاه داده
$db = Database::getInstance();
$mysqli = $db->getConnection();

// چک کردن وجود توکن در پایگاه داده
$stmt = $mysqli->prepare("SELECT id FROM users WHERE token = ? AND token_created_at > NOW() - INTERVAL 1 HOUR");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token!']);
    $stmt->close();
    exit;
}

$stmt->close();

// پردازش فرم ارسال رمز عبور جدید
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $new_password = trim($data['new_password']);
    
    if (empty($new_password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password is required!']);
    } else {
        // هش کردن رمز عبور جدید
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // به‌روزرسانی رمز عبور و پاک کردن توکن
        $stmt = $mysqli->prepare("UPDATE users SET password = ?, token = NULL, token_created_at = NULL WHERE token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Password has been reset successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to reset password!']);
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
    <link rel="stylesheet" href="styles.css">
    <title>Reset Password</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
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
        <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Reset Password</h2>
            <form id="resetForm">
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-sm hover:bg-green-600">Reset Password</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const token = "<?php echo htmlspecialchars($token); ?>";
            const newPassword = document.getElementById('new_password').value;

            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    new_password: newPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred!'
                });
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
