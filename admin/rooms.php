<?php include 'header_admin.php'; 

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == 'approve') {
        mysqli_query($conn, "UPDATE motel SET approve = 4 WHERE id = $id");
        echo "<script>location.href='rooms.php';</script>";
    }
    if ($_GET['action'] == 'hide') {
        mysqli_query($conn, "UPDATE motel SET approve = 3 WHERE id = $id");
        echo "<script>location.href='rooms.php';</script>";
    }
    if ($_GET['action'] == 'delete') {
        mysqli_query($conn, "DELETE FROM motel WHERE id = $id");
        echo "<script>location.href='rooms.php';</script>";
    }
}

$where = "WHERE 1=1";
if (isset($_GET['user']) && !empty($_GET['user'])) {
    $u = $_GET['user'];
    $where .= " AND (u.username LIKE '%$u%' OR u.name LIKE '%$u%')";
}
if (isset($_GET['price_min']) && !empty($_GET['price_min'])) {
    $min = intval($_GET['price_min']);
    $where .= " AND m.price >= $min";
}
if (isset($_GET['price_max']) && !empty($_GET['price_max'])) {
    $max = intval($_GET['price_max']);
    $where .= " AND m.price <= $max";
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if ($_GET['action'] == 'approve') {
        mysqli_query($conn, "UPDATE motel SET approve = 4 WHERE id = $id");
        echo "<script>location.href='rooms.php';</script>";
    }
    if ($_GET['action'] == 'hide') {
        mysqli_query($conn, "UPDATE motel SET approve = 3 WHERE id = $id");
        echo "<script>location.href='rooms.php';</script>";
    }
}

$sql = "SELECT m.*, u.username, u.name as fullname 
        FROM motel m 
        JOIN users u ON m.user_id = u.id 
        $where 
        ORDER BY m.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<h3 class="fw-bold mb-4 text-dark">Quản lý Phòng trọ</h3>

<div class="card-custom">
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold text-muted small">Người đăng</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-user"></i></span>
                <input type="text" name="user" class="form-control" placeholder="Tên user..." value="<?= $_GET['user'] ?? '' ?>">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold text-muted small">Giá từ</label>
            <input type="number" name="price_min" class="form-control" placeholder="VNĐ" value="<?= $_GET['price_min'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold text-muted small">Giá đến</label>
            <input type="number" name="price_max" class="form-control" placeholder="VNĐ" value="<?= $_GET['price_max'] ?? '' ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-filter"></i> Lọc</button>
        </div>
    </form>
</div>

<div class="card-custom p-0 overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4 py-3">Tin đăng</th>
                <th>Người đăng</th>
                <th>Giá phòng</th>
                <th>Trạng thái</th>
                <th class="text-end pe-4">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td class="ps-4">
                    <div class="fw-bold text-dark text-truncate" style="max-width: 300px;"><?= $row['title'] ?></div>
                    <small class="text-muted"><i class="fa-regular fa-clock"></i> <?= date('d/m/Y', strtotime($row['created_at'])) ?></small>
                </td>
                <td>
                    <div class="fw-bold"><?= $row['fullname'] ?></div>
                    <small class="text-muted">@<?= $row['username'] ?></small>
                </td>
                <td class="text-danger fw-bold"><?= number_format($row['price']) ?> đ</td>
                <td>
                    <?php if($row['approve'] == 4 || $row['approve'] == 5): ?>
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">Đã duyệt</span>
                    <?php if($row['approve'] == 5) echo '<span class="badge bg-dark ms-1">Đã thuê</span>'; ?>
                    <?php else: ?>
                        <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill">Chờ duyệt (3)</span>
                    <?php endif; ?>
                </td>
                    <td class="text-end pe-4">
                        <?php if($row['approve'] == 3): ?>
                            <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-action" title="Duyệt bài"><i class="fa-solid fa-check"></i></a>
                        <?php else: ?>
                            <a href="?action=hide&id=<?= $row['id'] ?>" class="btn btn-warning btn-action text-white" title="Ẩn về trạng thái chờ"><i class="fa-solid fa-eye-slash"></i></a>
                        <?php endif; ?>
                    
                    <a href="../dashboard.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-info btn-action text-white" title="Xem trước"><i class="fa-solid fa-eye"></i></a>
                    <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa vĩnh viễn?')" class="btn btn-danger btn-action" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div> </body>
</html>