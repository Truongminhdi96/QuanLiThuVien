<?php
require_once("incfiles/connect.php");
$type = isset($_POST['theloai']) ? trim($_POST['theloai']) : '';

if (empty($type)) {
    echo 'Không bỏ trống trường giá trị';
} else {
    $query = "INSERT INTO theloai (name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $type); // "s" cho chuỗi
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo 'Thêm thành công';
    } else {
        echo 'Lỗi: Không thể thêm thể loại - ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>