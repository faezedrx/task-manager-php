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
        overflow: hidden;
    }
    .sidebar {
        width: 200px;
        background-color: #2c3e50;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px 0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    .sidebar.open {
        transform: translateX(0);
    }
    .user-item {
        margin-top: auto;
        background: linear-gradient(180deg, #a8e063, #56ab2f);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .user-photo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }
    .user-item p {
        color: #ffffff;
        font-size: 16px;
        margin: 0;
        font-weight: bold;
    }
    .sidebar-item {
        width: 60px;
        height: 120px;
        background: linear-gradient(180deg, #a8e063, #56ab2f);
        position: relative;
        border-radius: 12px;
        margin: 15px 0;
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
        height: 12px;
        background: white;
        left: 0;
        transform: translateY(-50%);
        border-radius: 6px;
    }
    .sidebar-item::before {
        top: 30%;
    }
    .sidebar-item::after {
        top: 70%;
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
            transform: translateY(-15px) rotate(5deg);
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
        font-size: 14px;
        display: block;
        line-height: 1.2;
    }
    .sidebar-content a:hover {
        text-decoration: underline;
    }
    .content {
        flex-grow: 1;
        padding: 20px;
        background-color: #ecf0f1;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
        margin-left: 200px;
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
<script>
    const menuButton = document.getElementById('menu-button');
    const sidebar = document.getElementById('sidebar');

    menuButton.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
</script>
