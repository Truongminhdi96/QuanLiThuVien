<?php
require_once("incfiles/connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra các trường bắt buộc
    if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['address']) || empty($_POST['student_id'])) {
        echo "Vui lòng điền đầy đủ tất cả các trường!";
        exit;
    }

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);

    // Kiểm tra mã số sinh viên đã tồn tại cho người dùng khác hay chưa
    $check_query = "SELECT * FROM account WHERE student_id = ? AND id != ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "si", $student_id, $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "Mã số sinh viên đã tồn tại!";
    } else {
        $query = "UPDATE account SET name = ?, phone = ?, address = ?, student_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $phone, $address, $student_id, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Cập nhật người dùng thành công!";
        } else {
            echo "Lỗi khi cập nhật người dùng: " . mysqli_error($conn);
        }
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>