<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_task'])) {
    // گرفتن اتصال به پایگاه داده از کلاس Singleton
    $db = Database::getInstance();
    $mysqli = $db->getConnection();

    $task_id = $_POST['task_id'];
    
    $sql = "DELETE FROM tasks WHERE id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $task_id);
    if ($stmt->execute()) {
        $swal_message = "Task deleted successfully";
        $swal_type = "success";
    } else {
        $swal_message = $stmt->error;
        $swal_type = "error";
    }
    $stmt->close();
}
?>
