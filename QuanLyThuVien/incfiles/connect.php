<?php
$host = "localhost";
$user = "root";
$pass = ""; // Mật khẩu mặc định của XAMPP thường rỗng
$db = "quanlithuvien"; // Thay bằng tên database thực tế của bạn
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
// Không cần mysqli_select_db vì database đã được chọn trong mysqli_connect
?>

