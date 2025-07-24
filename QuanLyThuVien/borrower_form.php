<?php 
require_once("incfiles/head.php");

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit;
}

$user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
$query = "SELECT * FROM account WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: manage_users.php");
    exit;
}
mysqli_stmt_close($stmt);
?>

<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <a href="manage_users.php" class="btn btn-primary" style="margin-bottom: 15px;">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Quay lại
            </a>
            <div class="panel panel-primary">
                <div class="panel-heading">Thêm Người Mượn Sách</div>
                <div class="panel-body">
                    <form id="form-add-borrower" method="POST">
                        <div class="form-group">
                            <label>Tên người mượn</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Mã số sinh viên</label>
                            <input type="text" class="form-control" name="student_id" value="<?php echo htmlspecialchars($user['student_id']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Chọn sách</label>
                            <select class="form-control" name="book_id" required>
                                <option value="">-- Chọn sách --</option>
                                <?php 
                                $book_query = "SELECT * FROM dsbook WHERE soluong > 0";
                                $book_result = mysqli_query($conn, $book_query);
                                while ($book = mysqli_fetch_array($book_result, MYSQLI_ASSOC)) {
                                    echo '<option value="' . $book['IDbook'] . '">' . htmlspecialchars($book['namebook']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ngày mượn</label>
                            <input type="date" class="form-control" name="borrow_date" required>
                        </div>
                        <div class="form-group">
                            <label>Ngày trả</label>
                            <input type="date" class="form-control" name="return_date" required>
                        </div>
                    </form>
                    <center>
                        <button onclick="addBorrower();" class="btn btn-info">
                            <div id="loadAddBorrower"><i class="fa fa-check-square-o" aria-hidden="true"></i> Thêm</div>
                        </button>
                        <div id="checkAddBorrower" style="color:red;"></div>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function addBorrower() {
    $('#loadAddBorrower').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Đang kiểm tra');
    var formData = $('#form-add-borrower').serializeArray();
    console.log(formData);
    setTimeout(function() {
        $('#checkAddBorrower').load('add_borrower.php', formData);
        $('#loadAddBorrower').html('<i class="fa fa-check-square-o" aria-hidden="true"></i> Thêm');
    }, 1000);
}
</script>

<?php require_once("incfiles/end.php"); ?>