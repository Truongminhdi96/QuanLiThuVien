<?php
require_once("incfiles/connect.php");

$id = isset($_POST['idbook']) ? (int)$_POST['idbook'] : 0;
$name = isset($_POST['tensach']) ? trim($_POST['tensach']) : '';
$sl = isset($_POST['soluong']) ? (int)$_POST['soluong'] : 0;
$tacgia = isset($_POST['tacgia']) ? trim($_POST['tacgia']) : '';
$theloai = isset($_POST['theloai']) ? (int)$_POST['theloai'] : 0;

if (empty($name) || empty($tacgia) || $sl <= 0 || $theloai <= 0 || $id <= 0) {
    echo 'Không bỏ trống các trường giá trị hoặc giá trị không hợp lệ';
} else {
    $query = "UPDATE dsbook SET namebook = ?, soluong = ?, tacgia = ?, theloai = ? WHERE IDbook = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sisii", $name, $sl, $tacgia, $theloai, $id); // Sửa "sisi" thành "sisii"
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo '
        <div class="alert alert-info alert-dismissible">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          <strong>Thông Báo!</strong> Chỉnh sửa thành công.
        </div>
        ';
    } else {
        echo 'Lỗi: Không thể chỉnh sửa sách - ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>