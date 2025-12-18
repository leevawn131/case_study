<?php
session_start();
include("connect.php");
if (!isset($_SESSION['login_fail'])) {
    $_SESSION['login_fail'] = 0;
}

$site_key = "6LfS7RwsAAAAABN0tJRnFAeQeSsz9uyPB87CeGTa";
$secret_key = "6LfS7RwsAAAAAKarXbP8AjHMhy2gPdKY4cvXQ5FF";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $allow_login = true;
    
    if ($_SESSION['login_fail'] >= 3) {
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            
            if (!$responseData->success) {
                $error = "Xác minh Captcha thất bại. Vui lòng thử lại.";
                $allow_login = false;
            }
        } else {
            $error = "Vui lòng xác nhận bạn không phải là người máy.";
            $allow_login = false;
        }
    }

    if ($allow_login) {
        if (empty($username) || empty($password)) {
            $error = "Vui lòng nhập đủ thông tin";
        } else {
            $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["username"] = $row["name"];
                $_SESSION["role"] = $row["role"];
                if($_SESSION["role"] == 0){
                    header("location: ./admin/index.php");
                    exit();
                }
                $_SESSION['login_fail'] = 0;

                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['login_fail']++;
                $con_lai = 3 - $_SESSION['login_fail'];
                
                if ($_SESSION['login_fail'] >= 3) {
                    $error = "Sai tài khoản hoặc mật khẩu. Vui lòng xác thực Captcha.";
                } else {
                    $error = "Sai tài khoản hoặc mật khẩu.";
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .text-orange { color: #d35400; font-weight: bold; }
        .text-price { color: #c0392b; font-weight: bold; font-size: 1.1rem; }
        .room-card { background: #fff; border-bottom: 1px solid #eee; padding: 15px; margin-bottom: 15px; transition: 0.3s; }
        .room-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .room-img-thumb { width: 100%; height: 130px; object-fit: cover; border-radius: 4px; }
        .badge-custom { position: absolute; top: 10px; left: 10px; background: #e67e22; color: white; padding: 2px 8px; font-size: 0.75rem; }
        .section-title { border-left: 5px solid #d35400; padding-left: 10px; margin: 30px 0 20px 0; font-weight: bold; color: #444; }
        .detail-img { width: 100%; max-height: 500px; object-fit: cover; border-radius: 5px; }
    </style>
    <title>Đăng nhập</title>
</head>
<body class="body">
        <?php include ("header.php"); ?>
        <div class="box-login-register">
            <div class="header-title">Đăng nhập</div>
            <div class="form-content">
                <?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
            <form method="post">
                <div class="form-group">
                    <label>Tài khoản:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>mật khẩu:</label>
                    <input type="password" name="password" required>
                </div>
                <?php if ($_SESSION['login_fail'] >= 3): ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
                <?php endif; ?>
                <div class="btn-wrapper">
                    <button type="submit" name="login" class="btn-register">Đăng nhập</button>
                    <a href="register.php" class="btn-login">Đăng kí</a>
                </div>
            </form>
            </div>
        </div>
</body>
</html>