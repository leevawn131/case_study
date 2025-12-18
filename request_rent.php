<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để gửi yêu cầu thuê!'); window.location.href='login.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $motel_id = intval($_GET['id']);
    $renter_id = $_SESSION['user_id'];

    $sql_room = "SELECT user_id, title, approve FROM motel WHERE id = $motel_id";
    $res_room = mysqli_query($conn, $sql_room);
    
    if (mysqli_num_rows($res_room) > 0) {
        $room = mysqli_fetch_assoc($res_room);
        $owner_id = $room['user_id'];

        if ($room['approve'] != 4) {
             echo "<script>alert('Rất tiếc, phòng này hiện không thể thuê (đã hết hoặc chờ duyệt).'); window.history.back();</script>";
             exit();
        }

        if ($owner_id == $renter_id) {
            echo "<script>alert('Bạn không thể thuê phòng của chính mình!'); window.history.back();</script>";
            exit();
        }

        $check = mysqli_query($conn, "SELECT * FROM rental_requests WHERE motel_id = $motel_id AND user_id = $renter_id");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Bạn đã gửi yêu cầu rồi!'); window.history.back();</script>";
        } else {
            $sql_insert = "INSERT INTO rental_requests (motel_id, user_id, owner_id) VALUES ($motel_id, $renter_id, $owner_id)";
            if (mysqli_query($conn, $sql_insert)) {
                echo "<script>alert('Đã gửi thông báo cho chủ nhà thành công!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Lỗi hệ thống!'); window.history.back();</script>";
            }
        }
    }
}
?>