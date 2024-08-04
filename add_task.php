<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $task = $mysqli->real_escape_string($_POST['task']);
    $task_date = $mysqli->real_escape_string($_POST['task_date']);
    
    $sql = "INSERT INTO tasks (user_id, task, task_date) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iss", $user_id, $task, $task_date);
    if ($stmt->execute()) {
        $swal_message = "New task added successfully";
        $swal_type = "success";
    } else {
        $swal_message = $stmt->error;
        $swal_type = "error";
    }
    $stmt->close();
}
?>
