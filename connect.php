<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gtpt";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, charset: 'utf8');

function time_elapsed_string($datetime, $full = false) {
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $weeks = floor($diff->d / 7);
    $days = $diff->d - ($weeks * 7);

    $string = [
        'y' => ['val' => $diff->y, 'text' => 'năm'],
        'm' => ['val' => $diff->m, 'text' => 'tháng'],
        'w' => ['val' => $weeks,   'text' => 'tuần'],
        'd' => ['val' => $days,    'text' => 'ngày'],
        'h' => ['val' => $diff->h, 'text' => 'giờ'],
        'i' => ['val' => $diff->i, 'text' => 'phút'],
        's' => ['val' => $diff->s, 'text' => 'giây'],
    ];

    $result = [];
    foreach ($string as $k => $v) {
        if ($v['val'] > 0) {
            $result[] = $v['val'] . ' ' . $v['text'];
        }
    }

    if (!$full) $result = array_slice($result, 0, 1);
    return $result ? implode(', ', $result) . ' trước' : 'Vừa xong';
}
?>