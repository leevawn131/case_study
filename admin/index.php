<?php include 'header_admin.php'; 

$total_rooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM motel"))['c'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role = 1"))['c'];
$pending_rooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM motel WHERE approve = 0"))['c'];

$current_month = date('m');
$current_year = date('Y');
$rooms_this_month = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM motel WHERE MONTH(created_at) = $current_month AND YEAR(created_at) = $current_year"))['c'];
?>

<h2 class="mb-4">Báo cáo thống kê</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm p-3 mb-2 bg-white rounded card-stat" style="border-color: #0d6efd;">
            <div class="small text-muted">Tổng số phòng</div>
            <div class="h3"><?= $total_rooms ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 mb-2 bg-white rounded card-stat" style="border-color: #198754;">
            <div class="small text-muted">Thành viên</div>
            <div class="h3"><?= $total_users ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 mb-2 bg-white rounded card-stat" style="border-color: #ffc107;">
            <div class="small text-muted">Chờ duyệt</div>
            <div class="h3"><?= $pending_rooms ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 mb-2 bg-white rounded card-stat" style="border-color: #dc3545;">
            <div class="small text-muted">Tin tháng <?= $current_month ?></div>
            <div class="h3"><?= $rooms_this_month ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">Tìm kiếm & Thống kê chi tiết</div>
    <div class="card-body">
        <form method="GET" action="rooms.php" class="row g-3">
            <div class="col-md-3">
                <label>Theo tài khoản đăng:</label>
                <input type="text" name="filter_user" class="form-control" placeholder="Nhập tên user...">
            </div>
            <div class="col-md-3">
                <label>Khoảng giá từ:</label>
                <input type="number" name="price_from" class="form-control" placeholder="VNĐ">
            </div>
            <div class="col-md-3">
                <label>Đến giá:</label>
                <input type="number" name="price_to" class="form-control" placeholder="VNĐ">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter"></i> Lọc dữ liệu</button>
            </div>
        </form>
    </div>
</div>

</div> </body>
</html>