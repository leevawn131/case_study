<?php
include 'connect.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

$sql = "SELECT m.*, d.name as district_name 
        FROM favorites f
        JOIN motel m ON f.motel_id = m.id
        LEFT JOIN districs d ON m.district_id = d.id
        WHERE f.user_id = $uid
        ORDER BY f.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <h3 class="mb-4 text-danger fw-bold"><i class="fa-solid fa-heart"></i> Phòng trọ đã thích</h3>
    
    <div class="row">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="position-relative">
                            <img src="uploads/<?= $row['images'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 m-2 rounded small fw-bold">
                                <?= number_format($row['price']/1000000, 1) ?> tr
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title text-truncate">
                                <a href="dashboard.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark fw-bold"><?= $row['title'] ?></a>
                            </h6>
                            <div class="small text-muted mb-2"><i class="fa-solid fa-location-dot"></i> <?= $row['district_name'] ?></div>
                            
                            <a href="toggle_like.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger w-100">
                                <i class="fa-solid fa-heart-crack"></i> Bỏ thích
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="fa-regular fa-heart fa-3x mb-3"></i>
                <p>Bạn chưa lưu phòng trọ nào.</p>
                <a href="dashboard.php" class="btn btn-primary">Khám phá ngay</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>