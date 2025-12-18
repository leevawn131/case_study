<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để đăng tin!'); window.location.href='login.php';</script>";
    exit();
}

$msg = "";
$err = "";

if (isset($_POST['post_room'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = intval($_POST['price']);
    $area = intval($_POST['area']);
    $district_id = intval($_POST['district_id']);
    $category_id = intval($_POST['category_id']); 
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO motel (title, description, price, area, address, district_id, category_id, user_id, phone, images, approve, count_view) 
                    VALUES ('$title', '$desc', $price, $area, '$address', $district_id, $category_id, $user_id, '$phone', '$filename', 3, 0)";
            
            if (mysqli_query($conn, $sql)) {
                $msg = "Đăng tin thành công! Tin của bạn đang chờ Admin duyệt.";
            } else {
                $err = "Lỗi SQL: " . mysqli_error($conn);
            }
        } else {
            $err = "Không thể tải ảnh lên server (Lỗi quyền ghi file).";
        }
    } else {
        $err = "Vui lòng chọn ảnh minh họa cho phòng trọ.";
    }
}

$districts = mysqli_query($conn, "SELECT * FROM districs");
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng tin mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square"></i> Đăng tin phòng trọ mới</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if($msg) echo "<div class='alert alert-success'><i class='fa-solid fa-check-circle'></i> $msg</div>"; ?>
                        <?php if($err) echo "<div class='alert alert-danger'><i class='fa-solid fa-triangle-exclamation'></i> $err</div>"; ?>

                        <form method="post" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tiêu đề bài đăng <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Phòng trọ giá rẻ, đầy đủ tiện nghi..." required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Giá cho thuê (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" class="form-control" placeholder="Nhập số tiền..." required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Diện tích (m²) <span class="text-danger">*</span></label>
                                    <input type="number" name="area" class="form-control" placeholder="Nhập diện tích..." required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Khu vực <span class="text-danger">*</span></label>
                                    <select name="district_id" class="form-select" required>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                        <?php while($d = mysqli_fetch_assoc($districts)): ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Loại phòng <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Chọn Loại phòng --</option>
                                        <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Địa chỉ chính xác</label>
                                <input type="text" name="address" class="form-control" placeholder="Số nhà, tên đường, phường/xã..." required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Số điện thoại liên hệ</label>
                                <input type="text" name="phone" class="form-control" value="<?= $_SESSION['phone'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Hình ảnh phòng trọ <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <div class="form-text">Nên chọn ảnh rõ nét, chụp toàn cảnh phòng.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả chi tiết</label>
                                <textarea name="description" class="form-control" rows="5" placeholder="Mô tả thêm về tiện ích: Điều hòa, nóng lạnh, chung chủ, giờ giấc..."></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="post_room" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fa-solid fa-paper-plane"></i> Đăng tin ngay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>