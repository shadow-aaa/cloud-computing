<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以录入题目");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $text = $_POST['text'];
    $type = $_POST['type'];
    $points = $_POST['points'];
    $answer = '';

    switch ($type) {
        case 'single':
            $answer = $_POST['single-correct'];
            break;
        case 'multiple':
            $answer = implode(',', $_POST['multiple-correct']);
            break;
        case 'true_false':
            $answer = $_POST['true-false-correct'];
            break;
        case 'blank':
            $answer = $_POST['blank-correct']; 
            break;
        case 'short_answer':
            $answer = ''; 
            break;
        default:
            die("未知的题目类型");
    }

    $sql = "INSERT INTO questions (text, type, answer, points) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $text, $type, $answer, $points);

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
    <h1>题目录入成功!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "question_entry.html";
    }, 1500);
    </script>
</body>

</html>
<?php
    } else {
        echo "题目录入失败: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>