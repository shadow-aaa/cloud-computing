<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以创建班级");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = $_POST['class_name'];
    $teacher_id = $_SESSION['user_id'];

    $sql = "INSERT INTO classes (name, teacher_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $class_name, $teacher_id);

    if ($stmt->execute()) {
        ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>班级创建成功!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "class_management.php";
    }, 1500);
    </script>
</body>
<?php
    } else {
        echo "班级创建失败: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>