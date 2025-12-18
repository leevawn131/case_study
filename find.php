<?php
session_start();
include("connect.php");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style> 
        .text-orange { color: #d35400; font-weight: bold; }
        .text-price { color: #c0392b; font-weight: bold; font-size: 1.1rem; }
        .room-card { background: #fff; border-bottom: 1px solid #eee; padding: 15px; margin-bottom: 15px; }
        .room-img-thumb { width: 100%; height: 130px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header-chung">
        <button onclick="window.location.href='dashboard.php'" class="nut-trangchu"><h1>Trang chủ</h1></button>
        </div>

    <div class="container py-4">
        <h3>Kết quả tìm kiếm:</h3>
        <div class="row">
        <?php
        if(isset($_POST["find"])){
            $search = $_POST["search"];
            $search = mysqli_real_escape_string($conn, $search);

            $sql = "SELECT m.*, d.name as district_name, c.name as category_name
                    FROM motel m 
                    LEFT JOIN districs d ON m.district_id = d.id
                    LEFT JOIN categories c ON m.category_id = c.id
                    WHERE m.title LIKE '%$search%' 
                       OR m.address LIKE '%$search%' 
                       OR d.name LIKE '%$search%'
                       OR m.area LIKE '%$search%'";
            
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    include 'card_template.php';
                }
            } else {
                echo "<p>Không tìm thấy phòng trọ nào phù hợp.</p>";
            }
        }
        ?>
        </div>
    </div>
</body>
</html>