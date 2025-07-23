<?php
require_once("incfiles/connect.php"); // Kết nối cơ sở dữ liệu

// Lấy tháng và năm từ biểu mẫu, mặc định là tháng hiện tại
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('m');
$selected_year = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Đếm tổng số sách đã mượn trong tháng
$total_borrowed_query = "SELECT COUNT(*) as total FROM nguoidung WHERE MONTH(ngaymuon) = ? AND YEAR(ngaymuon) = ?";
$stmt = mysqli_prepare($conn, $total_borrowed_query);
mysqli_stmt_bind_param($stmt, "ii", $selected_month, $selected_year);
mysqli_stmt_execute($stmt);
$total_borrowed_result = mysqli_stmt_get_result($stmt);
$total_borrowed = mysqli_fetch_assoc($total_borrowed_result)['total'];
mysqli_stmt_close($stmt);

// Đếm số sách đang mượn trong tháng
$on_loan_query = "SELECT COUNT(*) as total FROM nguoidung WHERE tinhtrang = 'Đang mượn' AND MONTH(ngaymuon) = ? AND YEAR(ngaymuon) = ?";
$stmt = mysqli_prepare($conn, $on_loan_query);
mysqli_stmt_bind_param($stmt, "ii", $selected_month, $selected_year);
mysqli_stmt_execute($stmt);
$on_loan_result = mysqli_stmt_get_result($stmt);
$on_loan = mysqli_fetch_assoc($on_loan_result)['total'];
mysqli_stmt_close($stmt);

// Đếm số sách đã trả trong tháng
$returned_query = "SELECT COUNT(*) as total FROM nguoidung WHERE tinhtrang = 'Đã trả' AND MONTH(ngaymuon) = ? AND YEAR(ngaymuon) = ?";
$stmt = mysqli_prepare($conn, $returned_query);
mysqli_stmt_bind_param($stmt, "ii", $selected_month, $selected_year);
mysqli_stmt_execute($stmt);
$returned_result = mysqli_stmt_get_result($stmt);
$returned = mysqli_fetch_assoc($returned_result)['total'];
mysqli_stmt_close($stmt);

// Thống kê số sách mượn theo thể loại trong tháng
$category_query = "
    SELECT t.name as theloai, COUNT(n.ID) as total_borrowed
    FROM nguoidung n
    JOIN dsbook d ON n.tensach = d.namebook
    JOIN theloai t ON d.theloai = t.ID
    WHERE MONTH(n.ngaymuon) = ? AND YEAR(n.ngaymuon) = ?
    GROUP BY t.ID, t.name
";
$stmt = mysqli_prepare($conn, $category_query);
mysqli_stmt_bind_param($stmt, "ii", $selected_month, $selected_year);
mysqli_stmt_execute($stmt);
$category_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê số sách mượn</title>
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
        h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .stats-box {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .stats-box div {
            flex: 1;
            background: #f9f9f9;
            padding: 15px;
            margin: 0 10px;
            border-radius: 4px;
            text-align: center;
        }
        .stats-box div h4 {
            margin: 0 0 10px;
            color: #333;
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
        .form-group {
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        select, input[type="submit"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thống kê số sách mượn</h2>
        <!-- Biểu mẫu chọn tháng và năm -->
        <form method="POST" class="form-group">
            <label for="month">Chọn tháng:</label>
            <select name="month" id="month">
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    $month = sprintf("%02d", $i);
                    echo "<option value='$month'" . ($month == $selected_month ? " selected" : "") . ">$month</option>";
                }
                ?>
            </select>
            <label for="year">Chọn năm:</label>
            <select name="year" id="year">
                <?php
                $current_year = date('Y');
                for ($i = $current_year - 5; $i <= $current_year; $i++) {
                    echo "<option value='$i'" . ($i == $selected_year ? " selected" : "") . ">$i</option>";
                }
                ?>
            </select>
            <input type="submit" value="Xem thống kê">
        </form>
        <!-- Hiển thị tổng quan -->
        <h3>Thống kê tháng <?php echo $selected_month . '/' . $selected_year; ?></h3>
        <div class="stats-box">
            <div>
                <h4>Tổng số sách đã mượn</h4>
                <p><?php echo $total_borrowed; ?></p>
            </div>
            <div>
                <h4>Sách đang mượn</h4>
                <p><?php echo $on_loan; ?></p>
            </div>
            <div>
                <h4>Sách đã trả</h4>
                <p><?php echo $returned; ?></p>
            </div>
        </div>
        <!-- Thống kê theo thể loại -->
        <h3>Thống kê theo thể loại</h3>
        <table>
            <tr>
                <th>Thể loại</th>
                <th>Số sách mượn</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($category_result)) {
                echo "<tr>
                    <td>{$row['theloai']}</td>
                    <td>{$row['total_borrowed']}</td>
                </tr>";
            }
            ?>
        </table>
        <a href="index.php" class="home-btn">Về trang chủ</a>
    </div>
    <?php mysqli_close($conn); ?>
</body>
</html>