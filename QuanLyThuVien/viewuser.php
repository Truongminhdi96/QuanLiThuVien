<?php
require_once("incfiles/connect.php"); // đường dẫn chính xác đến file

// Xử lý cập nhật tình trạng
if (isset($_POST['update_status']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "UPDATE nguoidung SET tinhtrang = 'Đã trả' WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM nguoidung");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách người mượn sách</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .action-btn:hover {
            background-color: #cc0000;
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
            margin: 20px auto;
        }
        .home-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Danh sách người mượn sách</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Tên sách</th>
                <th>Ngày mượn</th>
                <th>Tình trạng</th>
                <th>Hành động</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['ID']}</td>
                    <td>{$row['hoten']}</td>
                    <td>{$row['tensach']}</td>
                    <td>{$row['ngaymuon']}</td>
                    <td>{$row['tinhtrang']}</td>
                    <td>";
                if ($row['tinhtrang'] === "Đang mượn") {
                    echo "<form method='POST' style='display:inline;'>
                        <input type='hidden' name='id' value='{$row['ID']}'>
                        <input type='submit' name='update_status' value='Cập nhật thành Đã trả' class='action-btn'>
                    </form>";
                }
                echo "</td>
                </tr>";
            }
            ?>
        </table>
        <a href="index.php" class="home-btn">Về trang chủ</a>
    </div>
    <?php mysqli_close($conn); ?>
</body>
</html>