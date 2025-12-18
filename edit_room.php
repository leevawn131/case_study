<?php
include 'connect.php'; 
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='login.php';</script>";
    exit();
}

$msg = "";
$err = "";

if (!isset($_GET['id'])) {
    echo "<script>alert('Không tìm thấy phòng!'); location.href='info.php';</script>";
    exit();
}

$room_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM motel WHERE id = $room_id AND user_id = $user_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Bạn không có quyền sửa phòng này!'); location.href='info.php';</script>";
    exit();
}
$row = mysqli_fetch_assoc($result);

if (isset($_POST['update_room'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = intval($_POST['price']);
    $area = intval($_POST['area']);
    $district_id = intval($_POST['district_id']);
    $category_id = intval($_POST['category_id']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $image_update = $row['images'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $filename)) {
            $image_update = $filename;
        }
    }

    $sql_update = "UPDATE motel SET 
                   title='$title', price=$price, area=$area, address='$address', 
                   district_id=$district_id, category_id=$category_id, 
                   description='$desc', phone='$phone', images='$image_update' 
                   WHERE id=$room_id";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='info.php';</script>";
    } else {
        $err = "Lỗi SQL: " . mysqli_error($conn);
    }
}

$districts = mysqli_query($conn, "SELECT * FROM districs");
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-pen"></i> Sửa tin đăng</h4>
                </div>
                <div class="card-body">
                    <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="fw-bold">Tiêu đề</label>
                            <input type="text" name="title" class="form-control" value="<?= $row['title'] ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">Giá (VNĐ)</label>
                                <input type="number" name="price" class="form-control" value="<?= $row['price'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">Diện tích (m²)</label>
                                <input type="number" name="area" class="form-control" value="<?= $row['area'] ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">Quận/Huyện</label>
                                <select name="district_id" class="form-select" required>
                                    <?php while($d = mysqli_fetch_assoc($districts)): ?>
                                        <option value="<?= $d['id'] ?>" <?= $d['id'] == $row['district_id'] ? 'selected' : '' ?>><?= $d['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">Loại phòng</label>
                                <select name="category_id" class="form-select" required>
                                    <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?= isset($c['id'])?$c['id']:$c['id'] ?>" <?= (isset($c['id'])?$c['id']:$c['id']) == $row['category_id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" value="<?= $row['address'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">SĐT Liên hệ</label>
                            <input type="text" name="phone" class="form-control" value="<?= $row['phone'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Ảnh hiện tại</label><br>
                            <img src="uploads/<?= $row['images'] ?>" style="height: 100px; border-radius: 5px;">
                            <input type="file" name="image" class="form-control mt-2">
                            <small class="text-muted">Chọn ảnh mới nếu muốn thay đổi.</small>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control" rows="5"><?= $row['description'] ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="info.php" class="btn btn-secondary">Hủy bỏ</a>
                            <button type="submit" name="update_room" class="btn btn-primary fw-bold">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>