<?php
require_once("incfiles/connect.php");

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    echo "Lỗi kết nối cơ sở dữ liệu: " . mysqli_connect_error();
    exit;
}

// Tạo bảng borrowers nếu chưa tồn tại
$create_borrowers_table = "
CREATE TABLE IF NOT EXISTS `borrowers` (
  `borrow_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `book_id` INT(11) NOT NULL,
  `borrow_date` DATE NOT NULL,
  `return_date` DATE NOT NULL,
  `status` ENUM('Chưa trả', 'Đã trả') NOT NULL DEFAULT 'Chưa trả',
  PRIMARY KEY (`borrow_id`),
  FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `dsbook` (`IDbook`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
if (!mysqli_query($conn, $create_borrowers_table)) {
    echo "Lỗi khi tạo bảng borrowers: " . mysqli_error($conn);
    exit;
}

// Kiểm tra và thêm cột status nếu chưa tồn tại
$alter_borrowers_table = "
ALTER TABLE `borrowers`
ADD COLUMN IF NOT EXISTS `status` ENUM('Chưa trả', 'Đã trả') NOT NULL DEFAULT 'Chưa trả';
";
if (!mysqli_query($conn, $alter_borrowers_table)) {
    echo "Lỗi khi thêm cột status: " . mysqli_error($conn);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra các trường bắt buộc
    if (empty($_POST['user_id']) || empty($_POST['book_id']) || empty($_POST['borrow_date']) || empty($_POST['return_date'])) {
        echo "Vui lòng điền đầy đủ tất cả các trường!";
        exit;
    }

    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);
    $borrow_date = mysqli_real_escape_string($conn, $_POST['borrow_date']);
    $return_date = mysqli_real_escape_string($conn, $_POST['return_date']);

    // Kiểm tra ngày trả phải sau ngày mượn
    if (strtotime($return_date) <= strtotime($borrow_date)) {
        echo "Ngày trả phải sau ngày mượn!";
        exit;
    }

    // Kiểm tra số lượng sách còn lại
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
        echo "Sách không tồn tại hoặc đã hết!";
        exit;
    }

    // Thêm thông tin mượn sách
    $query = "INSERT INTO borrowers (user_id, book_id, borrow_date, return_date, status) VALUES (?, ?, ?, ?, 'Chưa trả')";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        echo "Lỗi chuẩn bị câu lệnh thêm mượn sách: " . mysqli_error($conn);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "iiss", $user_id, $book_id, $borrow_date, $return_date);

    if (mysqli_stmt_execute($stmt)) {
        // Giảm số lượng sách
        $update_book = "UPDATE dsbook SET soluong = soluong - 1 WHERE IDbook = ?";
        $update_stmt = mysqli_prepare($conn, $update_book);
        if (!$update_stmt) {
            echo "Lỗi chuẩn bị câu lệnh cập nhật sách: " . mysqli_error($conn);
            exit;
        }
        mysqli_stmt_bind_param($update_stmt, "i", $book_id);
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);

        // Chuyển hướng đến trang danh sách người mượn
        echo "<script>window.location.href='manage_borrowers.php';</script>";
    } else {
        echo "Lỗi khi thêm người mượn: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>