<?php
include 'connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$detail_room = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($conn, "UPDATE motel SET count_view = count_view + 1 WHERE id = $id");
    
    $sql_detail = "SELECT m.*, d.Name as district_name, c.name as category_name, u.name as user_name, u.phone as user_phone, u.avatar
                   FROM motel m 
                   LEFT JOIN districs d ON m.district_id = d.id
                   LEFT JOIN categories c ON m.category_id = c.id
                   LEFT JOIN users u ON m.user_id = u.id
                   WHERE m.id = $id";
    $result_detail = mysqli_query($conn, $sql_detail);
    if ($result_detail && mysqli_num_rows($result_detail) > 0) {
        $detail_room = mysqli_fetch_assoc($result_detail);
    }
}

if (!$detail_room) {
    $sql_newest = "SELECT m.*, d.Name as district_name, c.name as category_name, u.name as user_fullname, u.avatar as user_avatar 
                   FROM motel m 
                   LEFT JOIN districs d ON m.district_id = d.id 
                   LEFT JOIN categories c ON m.category_id = c.id 
                   LEFT JOIN users u ON m.user_id = u.id 
                   WHERE m.approve IN (4,5) ORDER BY created_at DESC LIMIT 8";
    $res_newest = mysqli_query($conn, $sql_newest);

    $sql_viewest = "SELECT m.*, d.Name as district_name, c.name as category_name, u.name as user_fullname, u.avatar as user_avatar 
                    FROM motel m 
                    LEFT JOIN districs d ON m.district_id = d.id 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    LEFT JOIN users u ON m.user_id = u.id 
                    WHERE m.approve IN (4, 5) ORDER BY count_view DESC LIMIT 4";
    $res_viewest = mysqli_query($conn, $sql_viewest);

    $sql_near = "SELECT m.*, d.Name as district_name, c.name as category_name, u.name as user_fullname, u.avatar as user_avatar 
                 FROM motel m 
                 LEFT JOIN districs d ON m.district_id = d.id 
                 LEFT JOIN categories c ON m.category_id = c.id 
                 LEFT JOIN users u ON m.user_id = u.id 
                 WHERE m.approve IN (4, 5) AND (m.address LIKE '%Vinh%' OR m.address LIKE '%Bến Thủy%') LIMIT 4";
    $res_near = mysqli_query($conn, $sql_near);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phòng Trọ Nghệ An</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    
    <style>
        body { background-color: #f5f7fa; font-family: sans-serif; }
        .hero-banner {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover; background-position: center; height: 350px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 30px;
        }
        .detail-img { width: 100%; height: 400px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .detail-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .section-title { font-weight: bold; border-left: 5px solid #0d6efd; padding-left: 10px; margin: 30px 0 20px 0; text-transform: uppercase; }
        
        .rented-label {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.7); color: white; padding: 10px 20px;
            font-weight: bold; font-size: 1.2rem; border-radius: 5px; border: 2px solid white;
        }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>

    <?php if ($detail_room): ?>
    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <div class="detail-box mb-4">
                    <div class="position-relative">
                        <img src="uploads/<?= $detail_room['images'] ?>" class="detail-img mb-3" 
                             style="<?= ($detail_room['approve'] == 2) ? 'filter: grayscale(100%);' : '' ?>"
                             onerror="this.src='https://placehold.co/800x500?text=No+Image'">
                        
                        <?php if($detail_room['approve'] == 2): ?>
                            <div class="rented-label">ĐÃ HẾT PHÒNG</div>
                        <?php endif; ?>
                    </div>
                    
                    <h2 class="fw-bold text-dark"><?= $detail_room['title'] ?></h2>
                    <div class="d-flex text-muted mb-3 small">
                        <span class="me-3"><i class="fa-regular fa-clock"></i> <?= isset($detail_room['created_at']) ? time_elapsed_string($detail_room['created_at']) : 'Vừa xong' ?></span>
                        <span><i class="fa-regular fa-eye"></i> <?= $detail_room['count_view'] ?> lượt xem</span>
                    </div>

                    <h3 class="text-danger fw-bold mb-3"><?= number_format($detail_room['price']) ?> VNĐ</h3>
                    <hr>
                    <h5 class="fw-bold">Mô tả chi tiết</h5>
                    <div class="text-secondary" style="line-height: 1.6;">
                        <?= nl2br($detail_room['description']) ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="detail-box sticky-top" style="top: 20px;">
                    <h5 class="text-center text-primary fw-bold mb-3">Thông tin liên hệ</h5>
                    <div class="text-center mb-3">
                        <img src="<?= !empty($detail_room['avatar']) ? 'avatar/'.$detail_room['avatar'] : 'https://placehold.co/100' ?>" 
                             class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="fw-bold mt-2"><?= $detail_room['user_name'] ?? 'Chủ trọ' ?></div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="tel:<?= $detail_room['user_phone'] ?>" class="btn btn-success fw-bold">
                            <i class="fa-solid fa-phone"></i> Gọi: <?= $detail_room['user_phone'] ?>
                        </a>

                        <?php if($detail_room['approve'] == 2): ?>
                             <button class="btn btn-secondary fw-bold" disabled>
                                <i class="fa-solid fa-lock"></i> Đã hết phòng
                             </button>
                        <?php else: ?>
                            <a href="request_rent.php?id=<?= $detail_room['id'] ?>" 
                               class="btn btn-warning text-white fw-bold"
                               onclick="return confirm('Gửi thông tin cho chủ nhà?')">
                                <i class="fa-solid fa-handshake"></i> Muốn thuê phòng này
                            </a>
                        <?php endif; ?>
                    </div>

                    <ul class="list-unstyled mt-4 small border-top pt-3">
                        <li class="mb-2"><i class="fa-solid fa-location-dot text-danger"></i> <?= $detail_room['address'] ?>, <?= $detail_room['district_name'] ?></li>
                        <li class="mb-2"><i class="fa-solid fa-ruler-combined text-primary"></i> Diện tích: <b><?= $detail_room['area'] ?> m²</b></li>
                        <li><i class="fa-solid fa-star text-warning"></i> Tiện ích: <?= $detail_room['utilities'] ?? 'Cơ bản' ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="hero-banner">
            <div class="text-center text-white">
                <h1 class="fw-bold display-5 mb-3">Tìm Phòng Trọ Nhanh Chóng</h1>
                <div class="bg-white p-2 rounded-pill shadow-lg d-inline-block" style="min-width: 500px;">
                    <form action="find.php" method="post" class="d-flex">
                        <input type="text" name="search" class="form-control border-0 rounded-pill shadow-none" placeholder="Tìm theo quận, giá tiền...">
                        <button type="submit" name="find" class="btn btn-primary rounded-pill px-4 fw-bold">Tìm kiếm</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <h4 class="section-title">Phòng trọ mới đăng</h4>
            <div class="row">
                <?php 
                if ($res_newest && mysqli_num_rows($res_newest) > 0) {
                    while($row = mysqli_fetch_assoc($res_newest)) { include 'card_template.php'; }
                } else { echo "<p class='text-muted'>Chưa có tin đăng.</p>"; }
                ?>
            </div>

            <h4 class="section-title" style="border-color: #dc3545;">Xem nhiều nhất</h4>
            <div class="row">
                <?php 
                if ($res_viewest && mysqli_num_rows($res_viewest) > 0) {
                    while($row = mysqli_fetch_assoc($res_viewest)) { include 'card_template.php'; }
                }
                ?>
            </div>
            
            <h4 class="section-title" style="border-color: #198754;">Gần Đại Học Vinh</h4>
            <div class="row">
                <?php 
                if ($res_near && mysqli_num_rows($res_near) > 0) {
                    while($row = mysqli_fetch_assoc($res_near)) { include 'card_template.php'; }
                } else { echo "<p class='text-muted ms-3'>Chưa có phòng nào khu vực này.</p>"; }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>