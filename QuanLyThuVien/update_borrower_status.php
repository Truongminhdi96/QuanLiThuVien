<?php
require_once("incfiles/connect.php");

if (isset($_GET['borrow_id']) && isset($_GET['status']) && isset($_GET['book_id'])) {
    $borrow_id = mysqli_real_escape_string($conn, $_GET['borrow_id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $book_id = mysqli_real_escape_string($conn, $_GET['book_id']);

    // Kiểm tra trạng thái hợp lệ
    if (!in_array($status, ['Chưa trả', 'Đã trả'])) {
        echo "Trạng thái không hợp lệ!";
        exit;
    }

    // Kiểm tra số lượng sách trước khi chuyển về "Chưa trả"
    if ($status === 'Chưa trả') {
        $book_query = "SELECT soluong FROM dsbook WHERE IDbook = ?";
        $stmt = mysqli_prepare($conn, $book_query);
        if (!$stmt) {
            echo "Lỗi chuẩn bị câu lệnh kiểm tra sách: " . mysqli_error($conn);
            exit;
        }
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $book_result = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($book_result);
        mysqli_stmt_close($stmt);

        if (!$book || $book['soluong'] <= 0) {
            echo "Sách không tồn tại hoặc đã hết, không thể chuyển về trạng thái chưa trả!";
            exit;
        }
    }

    // Cập nhật trạng thái
    $query = "UPDATE borrowers SET status = ? WHERE borrow_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo "Lỗi chuẩn bị câu lệnh cập nhật trạng thái: " . mysqli_error($conn);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "si", $status, $borrow_id);

    if (mysqli_stmt_execute($stmt)) {
        // Cập nhật số lượng sách
        if ($status === 'Đã trả') {
            $update_book = "UPDATE dsbook SET soluong = soluong + 1 WHERE IDbook = ?";
            $update_stmt = mysqli_prepare($conn, $update_book);
            if (!$update_stmt) {
                echo "Lỗi chuẩn bị câu lệnh cập nhật sách: " . mysqli_error($conn);
                exit;
            }
            mysqli_stmt_bind_param($update_stmt, "i", $book_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        } elseif ($status === 'Chưa trả') {
            $update_book = "UPDATE dsbook SET soluong = soluong - 1 WHERE IDbook = ?";
            $update_stmt = mysqli_prepare($conn, $update_book);
            if (!$update_stmt) {
                echo "Lỗi chuẩn bị câu lệnh cập nhật sách: " . mysqli_error($conn);
                exit;
            }
            mysqli_stmt_bind_param($update_stmt, "i", $book_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }
        header("Location: manage_borrowers.php");
    } else {
        echo "Lỗi khi cập nhật trạng thái: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Thiếu tham số yêu cầu!";
}
mysqli_close($conn);
?>