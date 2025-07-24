<?php 
require_once("incfiles/head.php");
?>

<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Nút Về trang chủ và Quay lại -->
            <a href="index.php" class="btn btn-primary" style="margin-bottom: 15px; margin-right: 10px;">
                <i class="fa fa-home" aria-hidden="true"></i> Về trang chủ
            </a>
            <a href="manage_users.php" class="btn btn-secondary" style="margin-bottom: 15px;">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Quản lý người dùng
            </a>
            <!-- Thanh tìm kiếm -->
            <div class="form-group" style="margin-bottom: 15px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm người mượn theo tên...">
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên người mượn</th>
                        <th>Mã số sinh viên</th>
                        <th>Tên sách</th>
                        <th>Ngày mượn</th>
                        <th>Ngày trả</th>
                        <th>Trạng thái</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "
                        SELECT b.borrow_id, a.name, a.student_id, d.namebook, b.borrow_date, b.return_date, b.status, b.book_id
                        FROM borrowers b
                        JOIN account a ON b.user_id = a.id
                        JOIN dsbook d ON b.book_id = d.IDbook
                    ";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        echo "<tr><td colspan='8'>Lỗi truy vấn: " . mysqli_error($conn) . "</td></tr>";
                    } elseif (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='8'>Chưa có người mượn sách nào.</td></tr>";
                    } else {
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['borrow_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['namebook']); ?></td>
                        <td><?php echo htmlspecialchars($row['borrow_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['return_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="update_borrower_status.php?borrow_id=<?php echo htmlspecialchars($row['borrow_id']); ?>&status=<?php echo $row['status'] === 'Chưa trả' ? 'Đã trả' : 'Chưa trả'; ?>&book_id=<?php echo htmlspecialchars($row['book_id']); ?>" 
                               onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái trả sách?');">
                                <i class="fa <?php echo $row['status'] === 'Chưa trả' ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                   style="color:<?php echo $row['status'] === 'Chưa trả' ? 'green' : 'red'; ?>; margin-right: 10px;" 
                                   aria-hidden="true" title="<?php echo $row['status'] === 'Chưa trả' ? 'Đánh dấu đã trả' : 'Đánh dấu chưa trả'; ?>"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).find("td:eq(1)").text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>

<?php require_once("incfiles/end.php"); ?>