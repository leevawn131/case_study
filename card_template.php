<?php
$rid = isset($row['id']) ? $row['id'] : (isset($row['id']) ? $row['id'] : 0);
$is_liked = false;
$heart_icon = "fa-regular fa-heart"; 
$heart_class = "text-secondary bg-light"; 

if (isset($_SESSION['user_id']) && $rid > 0) {
    $uid_check = $_SESSION['user_id'];
    $check_like = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = $uid_check AND motel_id = $rid");
    if ($check_like && mysqli_num_rows($check_like) > 0) {
        $is_liked = true; $heart_icon = "fa-solid fa-heart"; $heart_class = "text-danger bg-white"; 
    }
}
$u_avatar = !empty($row['user_avatar']) ? 'avatar/'.$row['user_avatar'] : 'https://placehold.co/30';
$u_name = !empty($row['user_fullname']) ? $row['user_fullname'] : 'Người dùng';
?>

<div class="col-md-3 col-sm-6 mb-4"> 
    <div class="card h-100 shadow-sm room-card hover-shadow transition-all border-0">
        <div class="position-relative">
            <a href="dashboard.php?id=<?= $rid ?>">
                <img src="uploads/<?= $row['images'] ?>" class="card-img-top" 
                     style="height: 200px; object-fit: cover; <?= ($row['approve'] == 5) ? 'filter: grayscale(100%);' : '' ?>" 
                     onerror="this.src='https://placehold.co/300x200?text=Phong+Tro'">
            </a>
            
            <?php if($row['approve'] == 5): ?>
                <div class="position-absolute top-50 start-50 translate-middle bg-dark text-white px-2 py-1 rounded fw-bold opacity-75 small text-nowrap">
                    HẾT PHÒNG
                </div>
            <?php else: ?>
                <span class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 m-2 rounded small fw-bold shadow-sm">
                    <?= number_format($row['price']/1000000, 1) ?> tr
                </span>
            <?php endif; ?>

            <a href="toggle_like.php?id=<?= $rid ?>" 
               class="position-absolute top-0 start-0 px-2 py-1 m-2 rounded-circle shadow-sm text-decoration-none <?= $heart_class ?>" 
               title="<?= $is_liked ? 'Bỏ thích' : 'Lưu tin' ?>"
               style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                <i class="<?= $heart_icon ?>"></i>
            </a>
        </div>
        
        <div class="card-body p-3">
            <h6 class="card-title text-truncate mb-1">
                <a href="dashboard.php?id=<?= $rid ?>" class="text-decoration-none text-dark fw-bold" title="<?= $row['title'] ?>">
                    <?= $row['title'] ?>
                </a>
            </h6>
            <div class="small text-muted mb-2 text-truncate">
                <i class="fa-solid fa-location-dot text-danger me-1"></i> 
                <?= isset($row['district_name']) ? $row['district_name'] : 'Nghệ An' ?>
            </div>
            
            <div class="small mb-2">
                <?php if($row['approve'] == 5): ?>
                    <span class="text-secondary fw-bold"><i class="fa-solid fa-lock"></i> Đã có người thuê</span>
                <?php else: ?>
                    <span class="text-success fw-bold"><i class="fa-solid fa-door-open"></i> Đang còn trống</span>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center small border-top pt-2 mt-2">
                <img src="<?= $u_avatar ?>" class="rounded-circle me-2 border" style="width: 25px; height: 25px; object-fit: cover;">
                <div class="text-truncate text-muted fw-bold" style="max-width: 150px;">
                    <?= $u_name ?>
                </div>
            </div>
        </div>
    </div>
</div>