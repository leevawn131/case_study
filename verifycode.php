<?php
session_start();
// Nếu chưa nhập email ở bước 1 thì đá về bước 1
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = "";

if (isset($_POST['verify'])) {
    $code = $_POST['code'];
    
    // Kiểm tra mã nhập vào có khớp với mã trong Session không
    if ($code == $_SESSION['reset_code']) {
        // Mã đúng -> Đánh dấu là đã xác minh xong
        $_SESSION['is_verified'] = true;
        header("Location: resetpass.php");
        exit();
    } else {
        $error = "Mã xác nhận không đúng!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Nhập mã xác nhận - Bước 2</title>
</head>
<body class="body">
    <?php include ("header.php"); ?>
    <div class="box-login-register">
        <div class="header-title">Nhập mã xác thực (Bước 2/3)</div>
        <div class="form-content">
            <div style="text-align:center; margin-bottom:20px; color:green;">
                Chúng tôi đã gửi mã đến: <b><?= $_SESSION['reset_email'] ?></b>
                <br>
            </div>

            <?php if($error) echo "<p class='error-msg'>$error</p>"; ?>
            
            <form method="post">
                <div class="form-group">
                    <label>Mã xác nhận:</label>
                    <input type="text" name="code" required placeholder="Nhập mã 6 số...">
                </div>
                <div class="btn-wrapper">
                    <button type="submit" name="verify" class="btn-register" style="width:100%">Xác nhận</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>