<<<<<<< HEAD
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'dashboard-styles.php';
require_once 'sidebar.php';
require_once 'config.php';
require_once 'email.php';

if (!isset($_SESSION['username'])) {
    die('User not logged in');
}

$db = Database::getInstance();
$mysqli = $db->getConnection();

$sql = "SELECT id, username FROM users WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$user_email = $user['username'];
$stmt->close();

$swal_message = "";
$swal_type = "";
$swal_timer = 3000;

require_once 'add_task.php';
require_once 'edit_task.php';
require_once 'delete_task.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_now_for_date'])) {
    $task_date = $_POST['task_date'];
    
    $sql = "SELECT task FROM tasks WHERE user_id=? AND task_date=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $user_id, $task_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $tasks = [];
    while ($task_info = $result->fetch_assoc()) {
        $tasks[] = $task_info['task'];
    }

    if (!empty($tasks)) {
        $task_list = implode("<br>", array_map('htmlspecialchars', $tasks));
        $template = file_get_contents('email_template.html');
        $subject = "Reminder: Your Tasks for $task_date";
        $body = str_replace('{{task_list}}', $task_list, $template);

        if (sendEmail($user_email, $subject, $body)) {
            $swal_message = "Email sent successfully (check spam)";
            $swal_type = "success";
        } else {
            $swal_message = "Message could not be sent.";
            $swal_type = "error";
        }
    } else {
        $swal_message = "No tasks found for this date";
        $swal_type = "error";
    }
    $stmt->close();
}

$sql = "SELECT * FROM tasks WHERE user_id=? ORDER BY task_date";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks_by_date = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['task_date'];
    if (!isset($tasks_by_date[$date])) {
        $tasks_by_date[$date] = [];
    }
    $tasks_by_date[$date][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="icon" href="SERVICE-B.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            overflow-y: auto;
            font-family: 'Inter', sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
        }
        .shadow-glow {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .custom-button {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .custom-button:hover {
            transform: translateY(-2px);
        }
        .past-task {
            opacity: 0.5;
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="flex-1 p-6">
        <div class="max-w-md mx-auto bg-white p-6 rounded shadow-glow">
            <div class="header text-center mb-6">
                <h2 class="text-2xl font-bold">Add New Task</h2>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="task" class="block text-sm font-medium text-gray-700">Task</label>
                    <input type="text" id="task" name="task" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="task_date" class="block text-sm font-medium text-gray-700">Task Date</label>
                    <input type="date" id="task_date" name="task_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <button type="submit" name="add_task" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow-glow custom-button">Add Task</button>
            </form>
        </div>

        <div class="w-full max-w-3xl mx-auto bg-white p-6 rounded shadow-glow mt-6">
            <div class="header text-center mb-6">
                <h2 class="text-2xl font-bold">Your Tasks</h2>
            </div>
            <?php if (!empty($tasks_by_date)): ?>
                <?php foreach ($tasks_by_date as $date => $tasks): ?>
                    <?php $is_past = strtotime($date) < strtotime(date('Y-m-d')); ?>
                    <div class="mb-4 <?php echo $is_past ? 'past-task' : ''; ?>">
                        <h3 class="text-xl font-semibold mb-2 text-purple-700"><?php echo htmlspecialchars($date); ?></h3>
                        <div class="space-y-2">
                            <?php foreach ($tasks as $task): ?>
                                <div class="bg-white p-4 rounded shadow flex justify-between items-center border border-gray-200">
                                    <div>
                                        <p class="text-gray-800"><?php echo htmlspecialchars($task['task']); ?></p>
                                        <p class="text-gray-600 text-sm">Date: <?php echo htmlspecialchars($task['task_date']); ?></p>
                                    </div>
                                    <div class="space-x-2">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                            <button type="submit" name="delete_task" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow-glow custom-button">Delete</button>
                                        </form>
                                        <button onclick="showEditForm('<?php echo $task['id']; ?>', '<?php echo htmlspecialchars($task['task']); ?>', '<?php echo $task['task_date']; ?>')" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded shadow-glow custom-button">Edit</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <form method="POST" class="mt-4">
                                <input type="hidden" name="task_date" value="<?php echo htmlspecialchars($date); ?>">
                                <button type="submit" name="send_now_for_date" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded shadow-glow custom-button">Send All Tasks for This Date</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600">No tasks found</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
            <h2 class="text-xl font-bold mb-4">Edit Task</h2>
            <form method="POST">
                <input type="hidden" name="task_id" id="edit_task_id">
                <div class="mb-4">
                    <label for="edit_task" class="block text-sm font-medium text-gray-700">Task</label>
                    <input type="text" id="edit_task" name="edit_task" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="edit_task_date" class="block text-sm font-medium text-gray-700">Task Date</label>
                    <input type="date" id="edit_task_date" name="edit_task_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" name="edit_task_submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow-glow custom-button">Save Changes</button>
                <button type="button" onclick="hideEditForm()" class="w-full mt-2 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 rounded shadow-glow custom-button">Cancel</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swalMessage = '<?php echo $swal_message; ?>';
            const swalType = '<?php echo $swal_type; ?>';
            const swalTimer = <?php echo $swal_timer; ?>;

            if (swalMessage) {
                Swal.fire({
                    title: swalMessage,
                    icon: swalType,
                    timer: swalTimer,
                    showConfirmButton: false
                });
            }
        });

        function showEditForm(taskId, task, taskDate) {
            document.getElementById('edit_task_id').value = taskId;
            document.getElementById('edit_task').value = task;
            document.getElementById('edit_task_date').value = taskDate;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function hideEditForm() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>

<?php
$mysqli->close();
?>
=======
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'dashboard-styles.php';
require_once 'sidebar.php';
require_once 'config.php';
require_once 'email.php';

if (!isset($_SESSION['username'])) {
    die('User not logged in');
}

$db = Database::getInstance();
$mysqli = $db->getConnection();

$sql = "SELECT id, username FROM users WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$user_email = $user['username'];
$stmt->close();

$swal_message = "";
$swal_type = "";
$swal_timer = 3000;

require_once 'add_task.php';
require_once 'edit_task.php';
require_once 'delete_task.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_now_for_date'])) {
    $task_date = $_POST['task_date'];
    
    $sql = "SELECT task FROM tasks WHERE user_id=? AND task_date=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $user_id, $task_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $tasks = [];
    while ($task_info = $result->fetch_assoc()) {
        $tasks[] = $task_info['task'];
    }

    if (!empty($tasks)) {
        $task_list = implode("<br>", array_map('htmlspecialchars', $tasks));
        $template = file_get_contents('email_template.html');
        $subject = "Reminder: Your Tasks for $task_date";
        $body = str_replace('{{task_list}}', $task_list, $template);

        if (sendEmail($user_email, $subject, $body)) {
            $swal_message = "Email sent successfully (check spam)";
            $swal_type = "success";
        } else {
            $swal_message = "Message could not be sent.";
            $swal_type = "error";
        }
    } else {
        $swal_message = "No tasks found for this date";
        $swal_type = "error";
    }
    $stmt->close();
}

$sql = "SELECT * FROM tasks WHERE user_id=? ORDER BY task_date";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks_by_date = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['task_date'];
    if (!isset($tasks_by_date[$date])) {
        $tasks_by_date[$date] = [];
    }
    $tasks_by_date[$date][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="icon" href="SERVICE-B.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            overflow-y: auto;
            font-family: 'Inter', sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
        }
        .shadow-glow {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .custom-button {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .custom-button:hover {
            transform: translateY(-2px);
        }
        .past-task {
            opacity: 0.5;
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="flex-1 p-6">
        <div class="max-w-md mx-auto bg-white p-6 rounded shadow-glow">
            <div class="header text-center mb-6">
                <h2 class="text-2xl font-bold">Add New Task</h2>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="task" class="block text-sm font-medium text-gray-700">Task</label>
                    <input type="text" id="task" name="task" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="task_date" class="block text-sm font-medium text-gray-700">Task Date</label>
                    <input type="date" id="task_date" name="task_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <button type="submit" name="add_task" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow-glow custom-button">Add Task</button>
            </form>
        </div>

        <div class="w-full max-w-3xl mx-auto bg-white p-6 rounded shadow-glow mt-6">
            <div class="header text-center mb-6">
                <h2 class="text-2xl font-bold">Your Tasks</h2>
            </div>
            <?php if (!empty($tasks_by_date)): ?>
                <?php foreach ($tasks_by_date as $date => $tasks): ?>
                    <?php $is_past = strtotime($date) < strtotime(date('Y-m-d')); ?>
                    <div class="mb-4 <?php echo $is_past ? 'past-task' : ''; ?>">
                        <h3 class="text-xl font-semibold mb-2 text-purple-700"><?php echo htmlspecialchars($date); ?></h3>
                        <div class="space-y-2">
                            <?php foreach ($tasks as $task): ?>
                                <div class="bg-white p-4 rounded shadow flex justify-between items-center border border-gray-200">
                                    <div>
                                        <p class="text-gray-800"><?php echo htmlspecialchars($task['task']); ?></p>
                                        <p class="text-gray-600 text-sm">Date: <?php echo htmlspecialchars($task['task_date']); ?></p>
                                    </div>
                                    <div class="space-x-2">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                            <button type="submit" name="delete_task" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow-glow custom-button">Delete</button>
                                        </form>
                                        <button onclick="showEditForm('<?php echo $task['id']; ?>', '<?php echo htmlspecialchars($task['task']); ?>', '<?php echo $task['task_date']; ?>')" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded shadow-glow custom-button">Edit</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <form method="POST" class="mt-4">
                                <input type="hidden" name="task_date" value="<?php echo htmlspecialchars($date); ?>">
                                <button type="submit" name="send_now_for_date" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded shadow-glow custom-button">Send All Tasks for This Date</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600">No tasks found</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
            <h2 class="text-xl font-bold mb-4">Edit Task</h2>
            <form method="POST">
                <input type="hidden" name="task_id" id="edit_task_id">
                <div class="mb-4">
                    <label for="edit_task" class="block text-sm font-medium text-gray-700">Task</label>
                    <input type="text" id="edit_task" name="edit_task" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="edit_task_date" class="block text-sm font-medium text-gray-700">Task Date</label>
                    <input type="date" id="edit_task_date" name="edit_task_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" name="edit_task_submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow-glow custom-button">Save Changes</button>
                <button type="button" onclick="hideEditForm()" class="w-full mt-2 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 rounded shadow-glow custom-button">Cancel</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swalMessage = '<?php echo $swal_message; ?>';
            const swalType = '<?php echo $swal_type; ?>';
            const swalTimer = <?php echo $swal_timer; ?>;

            if (swalMessage) {
                Swal.fire({
                    title: swalMessage,
                    icon: swalType,
                    timer: swalTimer,
                    showConfirmButton: false
                });
            }
        });

        function showEditForm(taskId, task, taskDate) {
            document.getElementById('edit_task_id').value = taskId;
            document.getElementById('edit_task').value = task;
            document.getElementById('edit_task_date').value = taskDate;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function hideEditForm() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('menu-button');
        const sidebar = document.getElementById('sidebar');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    });
</script>

</body>
</html>

<?php
$mysqli->close();
?>
>>>>>>> e26e015 (update home and tasks)
