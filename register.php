<?php
include("connect.php");

$site_key = "6LfS7RwsAAAAABN0tJRnFAeQeSsz9uyPB87CeGTa";   
$secret_key = "6LfS7RwsAAAAAKarXbP8AjHMhy2gPdKY4cvXQ5FF"; 

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $repass = $_POST['repassword'];
    $displayname = $_POST['displayname'];

    $captcha_ok = false;
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if ($responseData->success) {
            $captcha_ok = true;
        }
    }

    if (!$captcha_ok) {
        $error = "Vui lòng xác nhận bạn không phải là người máy.";
    } else {
        
        $checkUserQuery = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $checkUserQuery);
        
        if(mysqli_num_rows($result) > 0){
            $error = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
        } else if (strlen($password) < 6){
            $error = "Mật khẩu phải ít nhất 6 kí tự.";
        } else if (!preg_match('/[a-zA-Z]/',$password) || !preg_match('/[0-9]/',$password)){
            $error = "Mật khẩu phải chứa cả chữ và số.";
        } else if ($password != $repass){
            $error = "Mật khẩu không khớp. Vui lòng thử lại.";
        } else {
            $insertUserQuery = "INSERT INTO users (username, password, email, name) VALUES ('$username', '$password', '$email', '$displayname')";
            if(mysqli_query($conn, $insertUserQuery)){
                echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='login.php';</script>";
            } else {
                $error = "Lỗi: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng kí thành viên</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .g-recaptcha { margin: 15px 0; display: flex; justify-content: center; }
    </style>
</head>
<body class="body">
    <?php include ("header.php"); ?>
    <div class="box-login-register">
        <div class="header-title">
            Đăng kí thành viên mới
        </div>

        <div class="form-content">
            <?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>

            <form method="post">
                <div class="form-group">
                    <label>Tài khoản:</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Mật khẩu:</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Nhập lại mật khẩu:</label>
                    <input type="password" name="repassword" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Tên hiển thị:</label>
                    <input type="text" name="displayname" required>
                </div>

                <div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>

                <div class="btn-wrapper">
                    <button type="submit" name="register" class="btn-register">Đăng kí</button>
                    <a href="login.php" class="btn-login">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>