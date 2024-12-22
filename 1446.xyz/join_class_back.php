<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'student') {
    die("只有学生可以加入班级");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_code = $_POST['class_code'];
    $student_id = $_SESSION['user_id'];

    // 获取班级ID
    $sql = "SELECT id FROM classes WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $class_code);
    $stmt->execute();
    $stmt->bind_result($class_id);
    $stmt->fetch();
    $stmt->close();

    if ($class_id) {
        // 检查是否已经加入过班级
        $sql = "SELECT COUNT(*) FROM student_classes WHERE student_id = ? AND class_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $student_id, $class_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            // 加入班级
            $sql = "INSERT INTO student_classes (student_id, class_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $student_id, $class_id);

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
    <h1>加入班级成功!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "join_class.php";
    }, 1500);
    </script>
</body>
<?php
            } else {
                echo "加入班级失败: " . $stmt->error;
            }

            $stmt->close();
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
    <h1>你已经加入了这个班级!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "join_class.php";
    }, 1500);
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
    <h1>班级不存在!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "join_class.php";
    }, 1500);
    </script>
</body>
<?php
    }

    $conn->close();
}
?>