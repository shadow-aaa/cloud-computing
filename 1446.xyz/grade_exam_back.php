<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以批改考试");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['scores']) && isset($_POST['exam_id'])) {
    $scores = $_POST['scores'];
    $exam_id = $_POST['exam_id'];

    foreach ($scores as $submission_id => $score) {
        $sql = "UPDATE submissions SET score = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $score, $submission_id);
        if (!$stmt->execute()) {
            echo "评分更新失败: " . $stmt->error;
            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>评分更新</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        text-align: center;
    }

    .container {
        width: 80%;
        margin: auto;
        overflow: hidden;
    }

    .message-container {
        background: #00000080;
        padding: 20px;
        border-radius: 30px;
        width: 60%;
        margin: 20px auto;
        color: white;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="message-container">
            <h1>评分更新成功!</h1>
        </div>
    </div>
    <script>
    setTimeout(function() {
        window.location.href = "teacher_dashboard.html";
    }, 1500);
    </script>
</body>

</html>
<?php
exit();
?>