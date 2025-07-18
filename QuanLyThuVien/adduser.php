<?php
require_once("incfiles/connect.php"); // Thay bằng đường dẫn chính xác đến connect.php

if (isset($_POST['submit'])) {
    $hoten = $_POST['hoten'];
    $tensach = $_POST['tensach'];
    $ngaymuon = $_POST['ngaymuon'];
    $tinhtrang = $_POST['tinhtrang'];

    // Sử dụng prepared statement để tránh SQL Injection
    $sql = "INSERT INTO nguoidung (hoten, tensach, ngaymuon, tinhtrang) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $hoten, $tensach, $ngaymuon, $tinhtrang);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Chuyển hướng đến viewuser.php sau khi thêm thành công
            header("Location: viewuser.php");
            exit();
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Lỗi chuẩn bị câu lệnh: " . mysqli_error($conn);
    }
    mysqli_close($conn); // Đóng kết nối
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm người mượn sách</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"], input[type="date"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .home-btn {
            display: block;
            width: 150px;
            background-color: #2196F3;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 4px;
            text-decoration: none;
            margin: 20px auto 0;
        }
        .home-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thêm người dùng mượn sách</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Họ tên người mượn:</label>
                <input type="text" name="hoten" required><br><br>
            </div>
            <div class="form-group">
                <label>Tên sách:</label>
                <input type="text" name="tensach" required><br><br>
            </div>
            <div class="form-group">
                <label>Ngày mượn:</label>
                <input type="date" name="ngaymuon" required><br><br>
            </div>
            <div class="form-group">
                <label>Tình trạng mượn:</label>
                <select name="tinhtrang" required>
                    <option value="Đang mượn">Đang mượn</option>
                    <option value="Đã trả">Đã trả</option>
                </select><br><br>
            </div>
            <input type="submit" name="submit" value="Thêm người mượn">
        </form>
        <a href="index.php" class="home-btn">Về trang chủ</a>
    </div>
</body>
</html>