<?php
session_start();
include("connect.php");

if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION["user_id"];
$msg = "";

// --- 1. XỬ LÝ: XÓA BÀI ĐĂNG (PHÒNG TRỌ) ---
if(isset($_GET['del_room'])){
    $room_id = intval($_GET['del_room']);
    $check_owner = mysqli_query($conn, "SELECT * FROM motel WHERE ID = $room_id AND user_id = $user_id");
    if(mysqli_num_rows($check_owner) > 0){
        // Xóa phòng thì xóa luôn các yêu cầu liên quan (Do database có ON DELETE CASCADE rồi nên ko cần xóa tay)
        mysqli_query($conn, "DELETE FROM motel WHERE ID = $room_id");
        echo "<script>alert('Đã xóa bài đăng!'); window.location.href='info.php';</script>";
    }
}

// --- 2. XỬ LÝ: CHỐT KHÁCH (Đã LH -> Khóa phòng) ---
if(isset($_GET['confirm_req'])){
    $req_id = intval($_GET['confirm_req']);
    
    // Lấy ID phòng
    $get_req = mysqli_query($conn, "SELECT motel_id FROM rental_requests WHERE id = $req_id AND owner_id = $user_id");
    
    if(mysqli_num_rows($get_req) > 0){
        $r = mysqli_fetch_assoc($get_req);
        $mid = $r['motel_id'];
        
        // B1: Đánh dấu yêu cầu là Đã chốt (status = 1)
        mysqli_query($conn, "UPDATE rental_requests SET status = 1 WHERE id = $req_id");
        
        // B2: TỰ ĐỘNG KHÓA PHÒNG (approve = 5: Đã thuê)
        mysqli_query($conn, "UPDATE motel SET approve = 5 WHERE ID = $mid");
        
        echo "<script>alert('Đã chốt khách! Phòng đã chuyển sang trạng thái ĐÃ THUÊ.'); location.href='info.php';</script>";
    }
}

// --- 3. XỬ LÝ: HỦY KÈO / KHÁCH BÙNG (Hủy LH -> Mở lại phòng) ---
if(isset($_GET['cancel_deal'])){
    $req_id = intval($_GET['cancel_deal']);
    
    // Lấy ID phòng
    $get_req = mysqli_query($conn, "SELECT motel_id FROM rental_requests WHERE id = $req_id AND owner_id = $user_id");
    
    if(mysqli_num_rows($get_req) > 0){
        $r = mysqli_fetch_assoc($get_req);
        $mid = $r['motel_id'];
        
        // B1: Đưa yêu cầu về trạng thái Mới (status = 0) hoặc Xóa (tùy bạn, ở đây mình để về 0 để biết khách này từng hủy)
        mysqli_query($conn, "UPDATE rental_requests SET status = 0 WHERE id = $req_id");
        
        // B2: TỰ ĐỘNG MỞ LẠI PHÒNG (approve = 4: Còn phòng)
        mysqli_query($conn, "UPDATE motel SET approve = 4 WHERE ID = $mid");
        
        echo "<script>alert('Đã hủy chốt! Phòng hiện đã CÒN TRỐNG trở lại.'); location.href='info.php';</script>";
    }
}

// --- 4. XỬ LÝ: XÓA YÊU CẦU (Nút X màu đỏ) ---
if(isset($_GET['del_req'])){
    $req_id = intval($_GET['del_req']);
    
    // Trước khi xóa, kiểm tra xem yêu cầu này có đang giữ phòng (status=1) không
    // Nếu đang chốt mà xóa yêu cầu -> Phòng phải mở lại
    $check_st = mysqli_query($conn, "SELECT status, motel_id FROM rental_requests WHERE id = $req_id AND owner_id = $user_id");
    if(mysqli_num_rows($check_st) > 0){
        $row_st = mysqli_fetch_assoc($check_st);
        if($row_st['status'] == 1) {
            // Nếu đang chốt mà xóa -> Mở lại phòng
             mysqli_query($conn, "UPDATE motel SET approve = 4 WHERE ID = " . $row_st['motel_id']);
        }
    }

    mysqli_query($conn, "DELETE FROM rental_requests WHERE id = $req_id AND owner_id = $user_id");
    echo "<script>alert('Đã xóa yêu cầu!'); location.href='info.php';</script>";
}

// (Các xử lý Upload, Info, Pass giữ nguyên)
if(isset($_POST['upload'])){
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0){
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if(in_array($_FILES['avatar']['type'], $allowed)){
            $target_dir = "avatar/";
            if(!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            $filename = $user_id . "_" . time() . "_" . basename($_FILES["avatar"]["name"]);
            if(move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir.$filename)){
                mysqli_query($conn, "UPDATE users SET avatar='$filename' WHERE id=$user_id");
                header("Refresh:0");
            }
        }
    }
}
if(isset($_POST['update_info'])){
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    if(mysqli_query($conn, "UPDATE users SET name='$fullname', phone='$phone', email='$email' WHERE id=$user_id")) $msg = "Cập nhật thành công!";
}
if(isset($_POST['change_pass'])){
    $old = $_POST['old_pass']; $new = $_POST['new_pass']; $confirm = $_POST['confirm_pass'];
    $user_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id=$user_id"));
    if($user_check['password'] != $old) $msg = "Mật khẩu cũ sai.";
    elseif($new != $confirm) $msg = "Mật khẩu xác nhận không khớp.";
    else { mysqli_query($conn, "UPDATE users SET password='$new' WHERE id=$user_id"); $msg = "Đổi mật khẩu thành công!"; }
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
$avatar = !empty($user['avatar']) ? "avatar/".$user['avatar'] : "https://placehold.co/150";
$my_rooms = mysqli_query($conn, "SELECT * FROM motel WHERE user_id = $user_id ORDER BY created_at DESC");

// Lấy danh sách yêu cầu thuê
$requests = mysqli_query($conn, "
    SELECT r.id as req_id, r.created_at, r.status, u.name as renter_name, u.phone as renter_phone, m.title as room_title
    FROM rental_requests r
    JOIN users u ON r.user_id = u.id
    JOIN motel m ON r.motel_id = m.ID
    WHERE r.owner_id = $user_id
    ORDER BY r.status DESC, r.created_at DESC 
"); 
// Sắp xếp: Đã chốt (1) lên đầu để dễ quản lý, sau đó mới đến mới nhất
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> 
    <title>Quản lý cá nhân</title>
</head>
<body>
    <div class="header-chung">
        <a href="dashboard.php" class="nut-trangchu text-decoration-none"><h1>Trang chủ</h1></a>
        <div style="margin-left: auto;">
             <span style="color: white; margin-right: 10px;">Xin chào, <?= $_SESSION['username'] ?></span>
             <a href="logout.php" class="nut-logout text-decoration-none">Đăng xuất</a>
        </div>
    </div>

<div class="container mt-5 pb-5">
    
    <?php if(mysqli_num_rows($requests) > 0): ?>
    <div class="alert alert-warning shadow-sm">
        <h5 class="fw-bold"><i class="fa-solid fa-bell text-danger"></i> Khách muốn thuê phòng</h5>
        <div class="table-responsive bg-white rounded mt-2">
            <table class="table table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th>Phòng quan tâm</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($req = mysqli_fetch_assoc($requests)): ?>
                    <tr class="<?= $req['status'] == 1 ? 'table-success' : '' ?>">
                        <td class="fw-bold"><?= $req['renter_name'] ?></td>
                        <td><a href="tel:<?= $req['renter_phone'] ?>" class="text-decoration-none fw-bold"><?= $req['renter_phone'] ?></a></td>
                        <td><?= $req['room_title'] ?></td>
                        <td>
                            <?php if($req['status'] == 0): ?>
                                <span class="badge bg-danger">Mới</span>
                            <?php else: ?>
                                <span class="badge bg-success">Đã chốt khách</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="tel:<?= $req['renter_phone'] ?>" class="btn btn-sm btn-success" title="Gọi ngay"><i class="fa-solid fa-phone"></i></a>
                            
                            <?php if($req['status'] == 0): ?>
                                <a href="?confirm_req=<?= $req['req_id'] ?>" class="btn btn-sm btn-primary" title="Xác nhận khách thuê"><i class="fa-solid fa-check"></i> Chốt khách</a>
                            
                            <?php else: ?>
                                <a href="?cancel_deal=<?= $req['req_id'] ?>" class="btn btn-sm btn-warning text-dark fw-bold" title="Khách hủy kèo -> Mở lại phòng" onclick="return confirm('Khách không thuê nữa? Phòng sẽ được mở lại.')"><i class="fa-solid fa-rotate-left"></i> Khách hủy</a>
                            <?php endif; ?>

                            <a href="?del_req=<?= $req['req_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa yêu cầu này?')" title="Xóa"><i class="fa-solid fa-xmark"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <h2 class="mb-4">Thông tin tài khoản</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="<?= $avatar ?>" class="rounded-circle mb-3 border" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?= $user['name'] ?></h4> 
                    <p class="text-muted"><?= $user['phone'] ?? 'Chưa cập nhật SĐT' ?></p>
                    <p class="text-muted"><?= $user['email'] ?? 'Chưa cập nhật SĐT' ?></p>
                    <form method="post" enctype="multipart/form-data">
                        <input type="file" name="avatar" class="form-control form-control-sm mb-2" required>
                        <button type="submit" name="upload" class="btn btn-primary btn-sm w-100">Đổi Avatar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#info">Thông tin</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pass">Đổi mật khẩu</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="info">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3"><label>Họ tên</label><input type="text" class="form-control" name="fullname" value="<?= $user['name'] ?>"></div>
                                <div class="mb-3"><label>Số điện thoại</label><input type="text" class="form-control" name="phone" value="<?= $user['phone'] ?>"></div>
                                <div class="mb-3"><label>Email</label><input type="text" class="form-control" name="email" value="<?= $user['email'] ?>"></div>
                                <button type="submit" name="update_info" class="btn btn-primary">Lưu thông tin</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pass">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3"><label>Mật khẩu cũ</label><input type="password" class="form-control" name="old_pass"></div>
                                <div class="mb-3"><label>Mật khẩu mới</label><input type="password" class="form-control" name="new_pass"></div>
                                <div class="mb-3"><label>Nhập lại mới</label><input type="password" class="form-control" name="confirm_pass"></div>
                                <button type="submit" name="change_pass" class="btn btn-danger">Đổi mật khẩu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-list-check"></i> Phòng đã đăng</h5>
                    <a href="post_room.php" class="btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Đăng tin</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tin đăng</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Tình trạng</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($my_rooms) > 0): ?>
                                    <?php while($room = mysqli_fetch_assoc($my_rooms)): $rid = isset($room['ID']) ? $room['ID'] : $room['id']; ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="uploads/<?= $room['images'] ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                <a href="dashboard.php?id=<?= $rid ?>" class="fw-bold text-decoration-none text-dark"><?= $room['title'] ?></a>
                                            </div>
                                        </td>
                                        <td class="text-danger fw-bold"><?= number_format($room['price']) ?></td>
                                        <td>
                                            <?php if($room['approve'] == 3) echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>'; 
                                                  elseif($room['approve'] == 4 || $room['approve'] == 5) echo '<span class="badge bg-success">Đã duyệt</span>'; ?>
                                        </td>
                                        <td>
                                            <?php if($room['approve'] == 3): ?>
                                                <small class="text-muted">--</small>
                                            <?php elseif($room['approve'] == 4): ?>
                                                <span class="text-success fw-bold"><i class="fa-solid fa-door-open"></i> Còn phòng</span>
                                            <?php elseif($room['approve'] == 5): ?>
                                                <span class="text-danger fw-bold"><i class="fa-solid fa-lock"></i> Đã thuê</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="edit_room.php?id=<?= $rid ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></a>
                                            <a href="info.php?del_room=<?= $rid ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-4">Chưa có bài đăng.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>