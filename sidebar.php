<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
<div id="sidebar" class="sidebar">
    <div class="sidebar-item bamboo">
        <div class="sidebar-content">
            <a href="home.php">Home</a>
        </div>
    </div>
    <div class="sidebar-item bamboo">
        <div class="sidebar-content">
            <a href="tasks.php">Tasks</a>
        </div>
    </div>
    <div class="user-item">
        <img src="user.jpg" alt="User Photo" class="user-photo">
        <p><?php echo htmlspecialchars($_SESSION['nickname']); ?>!</p>
        <a href="logout.php">Logout</a>
    </div>
</div>
