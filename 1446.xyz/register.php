<?php
require 'config.php'; // 包含数据库连接配置

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
</head>

<body>

    <h1>注册成功!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "index.html";
    }, 1500);
    </script>
</body>

</html>
<?php
    } else {
        echo "注册失败: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>