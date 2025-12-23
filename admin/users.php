<?php include 'header_admin.php'; 

if (isset($_POST['add_user'])) {
    $u = $_POST['username']; 
    $p = $_POST['password']; 
    $n = $_POST['name']; 
    $e = $_POST['email'];
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$u'");
    if(mysqli_num_rows($check) > 0) echo "<script>alert('Tên user đã tồn tại!');</script>";
    else {
        mysqli_query($conn, "INSERT INTO users (username, password, name, email, role) VALUES ('$u', '$p', '$n', '$e', 1)");
        echo "<script>location.href='users.php';</script>";
    }
}
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM motel WHERE user_id = $id");
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    echo "<script>location.href='users.php';</script>";
}
if (isset($_POST['reset_pass'])) {
    $uid = $_POST['user_id']; 
    $new = $_POST['new_pass'];
    mysqli_query($conn, "UPDATE users SET password = '$new' WHERE id = $uid");
    echo "<script>alert('Đã đổi mật khẩu!'); location.href='users.php';</script>";
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark m-0">Quản lý Người dùng</h3>
    <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-user-plus"></i> Thêm mới
    </button>
</div>

<div class="card-custom p-0 overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4 py-3">User info</th>
                <th>Liên hệ</th>
                <th>Vai trò</th>
                <th class="text-end pe-4">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 40px; height: 40px;">
                            <?= strtoupper(substr($row['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?= $row['username'] ?></div>
                            <small class="text-muted">ID: <?= $row['id'] ?></small>
                        </div>
                    </div>
                </td>
                <td>
                    <div><?= $row['name'] ?></div>
                    <small class="text-muted"><?= $row['email'] ?></small>
                </td>
                <td>
                    <?php if($row['role'] == 0): ?>
                        <span class="badge bg-danger rounded-pill px-3">Quản trị viên</span>
                    <?php else: ?>
                        <span class="badge bg-secondary rounded-pill px-3">Thành viên</span>
                    <?php endif; ?>
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-warning btn-action text-white" data-bs-toggle="modal" data-bs-target="#passModal<?= $row['id'] ?>" title="Đổi mật khẩu">
                        <i class="fa-solid fa-key"></i>
                    </button>
                    <?php if($row['role'] != 0): ?>
                    <a href="?del=<?= $row['id'] ?>" onclick="return confirm('Xóa user này sẽ mất hết bài đăng. Chắc chắn?')" class="btn btn-danger btn-action" title="Xóa">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    <?php endif; ?>

                    <div class="modal fade" id="passModal<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <form method="post" class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Đổi mật khẩu: <?= $row['username'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                    <input type="text" name="new_pass" class="form-control" placeholder="Nhập mật khẩu mới..." required>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="submit" name="reset_pass" class="btn btn-primary w-100">Lưu thay đổi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Thêm thành viên mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label>Họ tên</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add_user" class="btn btn-primary w-100 fw-bold">Thêm mới</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>