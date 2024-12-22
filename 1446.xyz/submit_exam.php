<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'student') {
    die("只有学生可以参加考试");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $exam_id = $_POST['exam_id'];
    $questions = $_POST['questions'];
    $types = $_POST['types'];
    $answers = $_POST['answers']; // 确保这里是 'answers' 而不是 'answer'

    $conn->begin_transaction();

    try {
        foreach ($questions as $index => $question_id) {
            $type = $types[$index];
            $answer = isset($answers[$question_id]) ? $answers[$question_id] : '';

            if ($type == 'multiple' && is_array($answer)) {
                $answer = implode(',', $answer); // 多选题答案处理
            }

            // 获取标准答案和题目分值
            $sql = "SELECT answer, points FROM questions WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $stmt->bind_result($correct_answer, $points);
            $stmt->fetch();
            $stmt->close();

            // 自动判题逻辑
            $score = null; // 简答题由教师评分
            if ($type == 'single' || $type == 'multiple' || $type == 'true_false' || $type == 'blank') {
                $score = ($answer == $correct_answer) ? $points : 0;
            }

            // 检查是否已有提交记录
            $sql = "SELECT * FROM submissions WHERE student_id = ? AND exam_id = ? AND question_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $student_id, $exam_id, $question_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // 更新已有记录
                $sql = "UPDATE submissions SET answer = ?, score = ? WHERE student_id = ? AND exam_id = ? AND question_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siiii", $answer, $score, $student_id, $exam_id, $question_id);
            } else {
                // 插入新记录
                $sql = "INSERT INTO submissions(student_id, exam_id, question_id, answer, score) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiisi", $student_id, $exam_id, $question_id, $answer, $score);
            }

            if (!$stmt->execute()) {
                throw new Exception("考试提交失败: " . $stmt->error);
            }

            $stmt->close();
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("提交答案时发生错误: " . $e->getMessage());
    } finally {
        $conn->close();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>考试提交成功!</h1>
    <script>
    setTimeout(function() {
        window.location.href = "student_dashboard.html";
    }, 1500);
    </script>
</body>

</html>
<?php
}
?>
