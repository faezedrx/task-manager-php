<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_task'])) {
    $task_id = $_POST['task_id'];
    $task = $mysqli->real_escape_string($_POST['task']);
    $task_date = $mysqli->real_escape_string($_POST['task_date']);
    
    // Get current date
    $current_date = date('Y-m-d');

    // Get current task date
    $sql = "SELECT task_date FROM tasks WHERE id=? AND user_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task_info = $result->fetch_assoc();

    if ($task_info) {
        $existing_task_date = $task_info['task_date'];
        
        // Compare task date with current date
        if ($existing_task_date >= $current_date) {
            // Task date has not passed
            $sql = "UPDATE tasks SET task=?, task_date=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $task, $task_date, $task_id);
            if ($stmt->execute()) {
                $swal_message = "Task updated successfully";
                $swal_type = "success";
            } else {
                $swal_message = $stmt->error;
                $swal_type = "error";
            }
        } else {
            // Task date has passed
            $swal_message = "Cannot edit a task with a past date";
            $swal_type = "error";
        }
    } else {
        $swal_message = "Task not found";
        $swal_type = "error";
    }
    $stmt->close();
}
?>
