<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            if ($role=='teacher') {
                header("Location: teacher_dashboard.html");
            }
            elseif ($role=='student') {
                header("Location: student_dashboard.html");
            }
        } else {
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>密码错误!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "login.html";
    }, 2000);
    </script>
</body>
<?php
                }
            } else {
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>用户名不存在!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "login.html";
    }, 1500);
    </script>
</body>
<?php
    }
    $stmt->close();
    $conn->close();
}
?>