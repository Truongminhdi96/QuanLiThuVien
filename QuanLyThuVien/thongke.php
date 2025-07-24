<?php 
require_once("incfiles/head.php");

// Truy vấn tổng số sách đang mượn
$total_borrowed_query = "SELECT COUNT(*) as total_borrowed FROM borrowers WHERE status = 'Chưa trả'";
$total_borrowed_result = mysqli_query($conn, $total_borrowed_query);
$total_borrowed = mysqli_fetch_assoc($total_borrowed_result)['total_borrowed'];
?>

<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Nút điều hướng -->
            <a href="index.php" class="btn btn-primary" style="margin-bottom: 15px; margin-right: 10px;">
                <i class="fa fa-home" aria-hidden="true"></i> Về trang chủ
            </a>

            <!-- Tổng số sách đang mượn -->
            <div class="alert alert-info" style="margin-bottom: 15px;">
                <strong>Tổng số sách đang mượn:</strong> <?php echo htmlspecialchars($total_borrowed); ?> cuốn
            </div>
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
                        <th>Số sách đang mượn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "
                        SELECT a.id, a.name, a.student_id, COUNT(b.borrow_id) as borrow_count
                        FROM account a
                        LEFT JOIN borrowers b ON a.id = b.user_id AND b.status = 'Chưa trả'
                        GROUP BY a.id, a.name, a.student_id
                    ";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        echo "<tr><td colspan='4'>Lỗi truy vấn: " . mysqli_error($conn) . "</td></tr>";
                    } elseif (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='4'>Chưa có người mượn sách nào.</td></tr>";
                    } else {
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['borrow_count']); ?></td>
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