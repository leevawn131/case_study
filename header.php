<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$link_redirect = "info.php"; 
if (isset($_SESSION['role']) && $_SESSION['role'] == 0) {
    $link_redirect = "admin/index.php"; 
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="header-chung" style="background-color: #212529; padding: 10px 20px; display: flex; align-items: center; color: white;">
    
    <a href="dashboard.php" class="text-decoration-none text-white me-4">
        <h2 class="m-0 fw-bold"><i class="fa-solid fa-house"></i> PT Nghệ An</h2>
    </a>

    <form action="find.php" method="post" class="d-flex" style="flex-grow: 1; max-width: 500px;">
        <input type="text" name="search" class="form-control rounded-0 rounded-start" placeholder="Tìm kiếm theo quận, giá...">
        <button type="submit" name="find" class="btn btn-primary rounded-0 rounded-end">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    <div class="ms-auto d-flex align-items-center">
        
        <?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
            <a href="post_room.php" class="btn btn-warning btn-sm fw-bold me-3 text-dark">
                <i class="fa-solid fa-plus"></i> Đăng tin
            </a>

            <a href="favorites.php" class="text-white me-3 fs-5" title="Tin đã thích">
                <i class="fa-solid fa-heart text-danger"></i>
            </a>

            <a href="<?= $link_redirect ?>" class="text-decoration-none text-white me-3 fw-bold">
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 0) echo '<i class="fa-solid fa-user-shield text-warning"></i>'; ?> 
                Xin chào, <?= $_SESSION['username'] ?>
            </a>

            <a href="logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>

        <?php else: ?>
            <a href="login.php" class="btn btn-primary me-2 fw-bold">Đăng nhập</a>
            <a href="register.php" class="btn btn-success fw-bold">Đăng ký</a>

        <?php endif; ?>
    </div>
</div>