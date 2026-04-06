<?php
session_start();
include("connect.php");

$error = "";

if (isset($_POST['check_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Kiểm tra email có trong DB không
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        // Lưu email và mã giả vào Session để dùng ở bước sau
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = "123456"; // Mã cố định giả vờ
        
        // Thông báo mã cho người dùng biết (Giả lập gửi mail)
        header("location: verifycode.php");
        exit();
    } else {
        $error = "Email này chưa đăng ký tài khoản nào.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Quên mật khẩu - Bước 1</title>
</head>
<body class="body">
    <?php include ("header.php"); ?>
    <div class="box-login-register">
        <div class="header-title">Khôi phục mật khẩu</div>
        <div class="form-content">
            <?php if($error) echo "<p class='error-msg'>$error</p>"; ?>
            
            <form method="post">
                <div class="form-group">
                    <label>Email của bạn:</label>
                    <input type="email" name="email" required placeholder="Nhập email đã đăng ký...">
                </div>
                <div class="btn-wrapper">
                    <button type="submit" name="check_email" class="btn-register" style="width:100%">Tiếp tục</button>
                </div>
                <div style="text-align:center; margin-top:15px;">
                    <a href="login.php" style="text-decoration:none; color:#666;">Quay lại đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>