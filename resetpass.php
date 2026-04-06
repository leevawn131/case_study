<?php
session_start();
include("connect.php");

if (!isset($_SESSION['is_verified']) || $_SESSION['is_verified'] !== true) {
    header("Location: forgot_password.php");
    exit();
}

$error = "";

if (isset($_POST['change_pass'])) {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $email = $_SESSION['reset_email'];

    if (strlen($new_pass) < 6) {
        $error = "Mật khẩu quá ngắn.";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {
        $sql = "UPDATE users SET password='$new_pass' WHERE email='$email'";
        if (mysqli_query($conn, $sql)) {
            session_unset(); session_destroy();
            echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='login.php';</script>";
        } else {
            $error = "Lỗi SQL: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Đặt lại mật khẩu</title>
</head>
<body class="body">
    <?php include ("header.php"); ?>
    <div class="box-login-register">
        <div class="header-title">Đặt mật khẩu mới (Bước 3/3)</div>
        <div class="form-content">
            <?php if($error) echo "<p class='error-msg'>$error</p>"; ?>
            <form method="post">
                <div class="form-group">
                    <label>Mật khẩu mới:</label>
                    <input type="password" name="new_pass" required>
                </div>
                <div class="form-group">
                    <label>Nhập lại:</label>
                    <input type="password" name="confirm_pass" required>
                </div>
                <div class="btn-wrapper">
                    <button type="submit" name="change_pass" class="btn-register" style="width:100%">Lưu mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>