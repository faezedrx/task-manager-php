<?php
require 'dashboard-styles.php';
require 'sidebar.php';
require 'config.php';
require 'email.php'; 

// Initialize session and check if user is logged in
if (!isset($_SESSION['username'])) {
    die('User not logged in');
}

// Get user ID and email from the database
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

// Include task operations
require 'add_task.php';
require 'edit_task.php';
require 'delete_task.php';

// Send immediate email for all tasks on a given date
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
        
        // Send email
        $subject = "Reminder: Your Tasks for $task_date";
        $body = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                    line-height: 1.6;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    padding: 10px 0;
                }
                .header h1 {
                    color: #5ab45c;
                }
                .content {
                    padding: 20px;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    color: #777;
                }
                .task-card {
                    background-color: #f9f9f9;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 10px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
                .task-card h3 {
                    margin: 0 0 10px;
                    color: #333;
                }
                .task-card p {
                    margin: 0;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Task Reminder</h1>
                </div>
                <div class="content">
                    ' . $task_list . '
                </div>
                <div class="footer">
                    <p>Best regards,<br>Your Task Management Team</p>
                </div>
            </div>
        </body>
        </html>';

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

// Get tasks and group by date
$sql = "SELECT * FROM tasks WHERE user_id=? ORDER BY task_date";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Group tasks by date
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
    <!-- Favicon -->
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
        <!-- Add New Task Form -->
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

        <!-- List of Tasks -->
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
                            <!-- Form for sending all tasks of the specific date -->
                            <form method="POST" class="mt-4">
                                <input type="hidden" name="task_date" value="<?php echo htmlspecialchars($date); ?>">
                                <button type="submit" name="send_now_for_date" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded shadow-glow custom-button">Send All Tasks for This Date</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600">No tasks found.</p>
            <?php endif; ?>
        </div>

        <!-- Edit Task Form -->
        <div id="edit-form" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden transition-opacity duration-300">
            <div class="bg-white p-6 rounded shadow-glow w-full max-w-md">
                <div class="header text-center mb-6">
                    <h2 class="text-2xl font-bold">Edit Task</h2>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" id="edit_task_id" name="task_id">
                    <div>
                        <label for="edit_task" class="block text-sm font-medium text-gray-700">Task</label>
                        <input type="text" id="edit_task" name="task" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="edit_task_date" class="block text-sm font-medium text-gray-700">Task Date</label>
                        <input type="date" id="edit_task_date" name="task_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <button type="submit" name="edit_task" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow-glow custom-button">Update Task</button>
                    <button type="button" onclick="hideEditForm()" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 rounded shadow-glow custom-button mt-2">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showEditForm(id, task, task_date) {
            document.getElementById('edit_task_id').value = id;
            document.getElementById('edit_task').value = task;
            document.getElementById('edit_task_date').value = task_date;
            document.getElementById('edit-form').classList.remove('hidden');
        }

        function hideEditForm() {
            document.getElementById('edit-form').classList.add('hidden');
        }

        <?php if ($swal_message != ""): ?>
        Swal.fire({
            title: "<?php echo $swal_message; ?>",
            icon: "<?php echo $swal_type; ?>",
            timer: <?php echo $swal_timer; ?>,
            showConfirmButton: false
        });
        <?php endif; ?>

        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('menu-button');
            const sidebar = document.getElementById('sidebar');

            if (menuButton && sidebar) {
                menuButton.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                });
            }
        });
    </script>
</body>
</html>

<?php
$mysqli->close();
?>