<?php 
require_once("incfiles/connect.php");
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Ép kiểu để đảm bảo $id là số nguyên

if ($id > 0) {
    $query = "DELETE FROM theloai WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id); // "i" cho số nguyên
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        echo 'Đã xóa
            <script type="text/javascript">
              window.location="/index.php";
            </script>';
    } else {
        echo 'Lỗi: Không thể xóa thể loại';
    }
    mysqli_stmt_close($stmt);
} else {
    echo 'Lỗi: ID không hợp lệ';
}
?>