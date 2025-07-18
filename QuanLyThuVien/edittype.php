<?php
require_once("incfiles/connect.php");

$id = isset($_POST['idtype']) ? (int)$_POST['idtype'] : 0;
$name = isset($_POST['theloai']) ? trim($_POST['theloai']) : '';

if (empty($name) || $id <= 0) {
    echo 'Không bỏ trống các trường giá trị hoặc ID không hợp lệ';
} else {
    $query = "UPDATE theloai SET name = ? WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $name, $id); // "si" cho chuỗi, số
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo '
        <div class="alert alert-info alert-dismissible">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          <strong>Thông Báo!</strong> Chỉnh sửa thành công.
        </div>
        ';
    } else {
        echo 'Lỗi: Không thể chỉnh sửa thể loại - ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>