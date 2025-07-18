<?php
require_once("incfiles/connect.php");

$name = isset($_POST['tensach']) ? trim($_POST['tensach']) : '';
$sl = isset($_POST['soluong']) ? (int)$_POST['soluong'] : 0;
$tacgia = isset($_POST['tacgia']) ? trim($_POST['tacgia']) : '';
$theloai = isset($_POST['theloai']) ? (int)$_POST['theloai'] : 0;

if (empty($name) || $sl <= 0 || empty($tacgia) || $theloai <= 0) {
    echo 'Không bỏ trống các trường giá trị hoặc giá trị không hợp lệ';
} else {
    $query = "INSERT INTO dsbook (namebook, theloai, tacgia, soluong) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sisi", $name, $theloai, $tacgia, $sl); // "sisi" cho chuỗi, số, chuỗi, số
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo 'Thêm thành công';
    } else {
        echo 'Lỗi: Không thể thêm sách - ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>