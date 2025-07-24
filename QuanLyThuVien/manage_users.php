<?php 
require_once("incfiles/head.php");

// Cập nhật bảng account để đảm bảo các cột cần thiết tồn tại
$alter_table_query = "
ALTER TABLE `account`
ADD COLUMN IF NOT EXISTS `name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
ADD COLUMN IF NOT EXISTS `phone` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
ADD COLUMN IF NOT EXISTS `address` TEXT COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
ADD COLUMN IF NOT EXISTS `student_id` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
";
mysqli_query($conn, $alter_table_query);

// Tạo bảng borrowers nếu chưa tồn tại
$create_borrowers_table = "
CREATE TABLE IF NOT EXISTS `borrowers` (
  `borrow_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `book_id` INT(11) NOT NULL,
  `borrow_date` DATE NOT NULL,
  `return_date` DATE NOT NULL,
  PRIMARY KEY (`borrow_id`),
  FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `dsbook` (`IDbook`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
mysqli_query($conn, $create_borrowers_table);
?>

<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <!-- Nút Về trang chủ -->
            <a href="index.php" class="btn btn-primary" style="margin-bottom: 15px;">
                <i class="fa fa-home" aria-hidden="true"></i> Về trang chủ
            </a>
            <!-- Thanh tìm kiếm -->
            <div class="form-group" style="margin-bottom: 15px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm người dùng theo tên...">
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Mã số sinh viên</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT * FROM account";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td>
                            <a href="borrower_form.php?user_id=<?php echo $row['id']; ?>" title="Thêm người mượn sách">
                                <i class="fa fa-book" style="color:green; margin-right: 10px;" aria-hidden="true"></i>
                            </a>
                            <a href="" data-toggle="modal" data-target="#editModal<?php echo $row['id']; ?>" title="Chỉnh sửa người dùng">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            </a>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?');" title="Xóa người dùng">
                                <i style="color:red; margin-left: 10px;" class="fa fa-times-circle" aria-hidden="true"></i>
                            </a>
                            <!-- Modal chỉnh sửa -->
                            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['id']; ?>">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            <h4 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Chỉnh sửa người dùng</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" id="form-edit-user<?php echo $row['id']; ?>">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="form-group">
                                                    <label>Tên</label>
                                                    <input class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Số điện thoại</label>
                                                    <input class="form-control" type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Địa chỉ</label>
                                                    <input class="form-control" type="text" name="address" value="<?php echo htmlspecialchars($row['address']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Mã số sinh viên</label>
                                                    <input class="form-control" type="text" name="student_id" value="<?php echo htmlspecialchars($row['student_id']); ?>">
                                                </div>
                                            </form>
                                            <div id="checkEdit<?php echo $row['id']; ?>"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button onclick="reload();" type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                            <button onclick="editUser(<?php echo $row['id']; ?>);" type="button" class="btn btn-primary">
                                                <div id="loadEditUser<?php echo $row['id']; ?>"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lưu</div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="5">
                            <form id="form-add-user" method="POST">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="name" placeholder="Nhập tên người dùng">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="phone" placeholder="Nhập số điện thoại">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="address" placeholder="Nhập địa chỉ">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="student_id" placeholder="Nhập mã số sinh viên">
                                </div>
                            </form>
                        </td>
                        <td>
                            <button onclick="addUser();" class="btn btn-info">
                                <div id="loadAddUser"><i class="fa fa-check-square-o" aria-hidden="true"></i> Thêm Người Dùng</div>
                            </button>
                             <button onclick="addUser();" class="btn btn-info">
                                 <a style="color: white;  text-decoration: none;" href="/manage_borrowers.php"><div ></i> Danh Sách Người Mượn</div></a>
                            </button>
                            <div id="checkAdd" style="color:red;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
function reload() {
    window.location.reload();
}

function addUser() {
    $('#loadAddUser').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Đang kiểm tra');
    var formData = $('#form-add-user').serializeArray();
    console.log(formData);
    setTimeout(function() {
        $('#checkAdd').load('add_user.php', formData);
        $('#loadAddUser').html('<i class="fa fa-check-square-o" aria-hidden="true"></i> Thêm');
    }, 1000);
}

function editUser(id) {
    $('#loadEditUser' + id).html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Đang kiểm tra');
    var formData = $('#form-edit-user' + id).serializeArray();
    console.log(formData);
    setTimeout(function() {
        $('#checkEdit' + id).load('edit_user.php', formData);
        $('#loadEditUser' + id).html('<i class="fa fa-check-square-o" aria-hidden="true"></i> Lưu');
    }, 1000);
}

$(document).ready(function() {
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr:not(:last-child)").filter(function() {
            $(this).toggle($(this).find("td:eq(1)").text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>

<?php require_once("incfiles/end.php"); ?>