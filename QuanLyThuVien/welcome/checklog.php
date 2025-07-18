<?php 
session_start();
require_once("../incfiles/connect.php");

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (!empty($username) && !empty($password)) {
    // Sử dụng prepared statement để tránh SQL Injection
    $query = "SELECT * FROM account WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password); // "ss" nghĩa là 2 chuỗi
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        echo "<b>Tên đăng nhập hoặc mật khẩu không đúng!</b>";
    } else {
        $_SESSION["username"] = $username;
        echo '<b>Đăng nhập thành công.</b>';
        echo '<script type="text/javascript">
                alert("Đăng nhập thành công");
                window.location="/index.php";
              </script>';
    }
    mysqli_stmt_close($stmt);
} else {
    echo '<b>Vui lòng nhập đầy đủ thông tin</b>';
}
?>