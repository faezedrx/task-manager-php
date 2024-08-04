<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<!-- Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<!-- Favicon -->
<link rel="icon" href="SERVICE-B.png" type="image/x-icon">
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        height: 100vh;
        overflow: hidden; /* جلوگیری از اضافه شدن اسکرول به بدنه */
    }

    .sidebar {
        width: 200px;
        background-color: #2c3e50;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px 0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        position: fixed; /* تغییر موقعیت به fixed */
        top: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%); /* Initially hidden */
        transition: transform 0.3s ease; /* اضافه کردن transition برای انیمیشن نرم تر */
    }

    .sidebar.open {
        transform: translateX(0); /* Show sidebar */
    }

    .user-item {
        margin-top: auto; /* Pushes the user-item to the bottom */
        background: linear-gradient(180deg, #a8e063, #56ab2f); /* Gradient background */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for better visibility */
        padding: 20px; /* Increased padding for better spacing */
        border-radius: 12px; /* Match the border-radius of other items */
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .user-photo {
        width: 80px; /* Adjust size as needed */
        height: 80px; /* Adjust size as needed */
        border-radius: 50%; /* Circular photo */
        object-fit: cover; /* Ensures photo covers the area without distortion */
        margin-bottom: 10px; /* Space between photo and text */
    }

    .user-item p {
        color: #ffffff; /* White text color */
        font-size: 16px; /* Increased font size for better readability */
        margin: 0; /* Remove default margin */
        font-weight: bold; /* Make text bold */
    }

    .sidebar-item {
        width: 60px; /* Increased width */
        height: 120px; /* Increased height */
        background: linear-gradient(180deg, #a8e063, #56ab2f);
        position: relative;
        border-radius: 12px; /* Slightly larger border-radius */
        margin: 15px 0; /* Increased margin */
        animation: sway 10s infinite ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-item::before,
    .sidebar-item::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 12px; /* Increased height of the lines */
        background: white;
        left: 0;
        transform: translateY(-50%);
        border-radius: 6px; /* Increased border-radius for lines */
    }

    .sidebar-item::before {
        top: 30%; /* Adjusted position for the top line */
    }

    .sidebar-item::after {
        top: 70%; /* Adjusted position for the bottom line */
    }

    .sidebar-item:nth-child(odd) {
        animation-duration: 15s;
    }

    .sidebar-item:nth-child(even) {
        animation-duration: 10s;
    }

    @keyframes sway {
        0% {
            transform: translateY(0) rotate(0deg);
        }
        50% {
            transform: translateY(-15px) rotate(5deg); /* Adjusted sway movement */
        }
        100% {
            transform: translateY(0) rotate(0deg);
        }
    }

    .sidebar-content {
        text-align: center;
    }

    .sidebar-content a {
        color: #ffffff;
        text-decoration: none;
        font-size: 14px; /* Increased font size */
        display: block;
        line-height: 1.2; /* Adjust line height */
    }

    .sidebar-content a:hover {
        text-decoration: underline;
    }

    .content {
        flex-grow: 1;
        padding: 20px;
        background-color: #ecf0f1;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
        margin-left: 200px; /* جابجایی محتوا به سمت راست به اندازه عرض نوار کناری */
    }

    .menu-button {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 1000;
    }
</style>
</head>
<body class="flex h-screen bg-gray-100">
<button id="menu-button" class="menu-button p-2 bg-green-500 text-white rounded-md">
    ☰
</button>

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
    <!-- <div class="sidebar-item bamboo">
        <div class="sidebar-content">
            <a href="logout.php">Logout</a>
        </div>
    </div> -->
    <div class="user-item">
        <img src="user.jpg" alt="User Photo" class="user-photo">
        <p><?php echo htmlspecialchars($_SESSION['nickname']); ?>!</p>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="content">
    <!-- محتوای اصلی صفحه -->
    <!-- <h1>Welcome to your Dashboard</h1>
    <p>This is the main content area.</p> -->
</div>

<script>
    const menuButton = document.getElementById('menu-button');
    const sidebar = document.getElementById('sidebar');

    menuButton.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
</script>
</body>
</html>
