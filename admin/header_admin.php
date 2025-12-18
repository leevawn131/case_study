<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../connect.php';

if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] != 0)) {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.location.href='../dashboard.php';</script>";
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản trị</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f3f4f6;
            overflow-x: hidden;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: #111827;
            color: #9ca3af;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 20px;
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #374151;
            background: #1f2937;
        }

        .sidebar-menu {
            padding: 20px 10px;
            flex-grow: 1;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: 0.3s;
            font-weight: 600;
        }

        .sidebar-menu a:hover {
            background-color: #374151;
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background-color: #3b82f6;
            color: #fff;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.4);
        }

        .sidebar-menu i {
            width: 25px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }

        .card-custom {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #374151;
            background: #1f2937;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-building-user"></i> ADMIN PANEL
    </div>
    
    <div class="sidebar-menu">
        <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-pie"></i> Thống kê báo cáo
        </a>
        <a href="rooms.php" class="<?= $current_page == 'rooms.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-house-laptop"></i> Quản lý Phòng trọ
        </a>
        <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-users-gear"></i> Quản lý Người dùng
        </a>
    </div>

    <div class="sidebar-footer">
        <a href="../dashboard.php" class="text-decoration-none text-light mb-2 d-block small">
            <i class="fa-solid fa-arrow-left"></i> Về trang web
        </a>
        <a href="../logout.php" class="text-decoration-none text-danger fw-bold d-block">
            <i class="fa-solid fa-power-off"></i> Đăng xuất
        </a>
    </div>
</div>

<div class="main-content">