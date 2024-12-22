<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'student') {
    echo "<script>setTimeout(function() {
                window.location.href = \"student_dashboard.html\";
            }, 1500);</script>";
    die("只有学生可以参加考试");
}

$student_id = $_SESSION['user_id'];
$current_time = date('Y-m-d H:i:s');

// 获取考试信息
$sql = "SELECT e.id AS exam_id, e.name AS exam_name, e.start_time, e.end_time, 
               c.name AS class_name, u.username AS teacher_name
        FROM exams e
        JOIN classes c ON e.class_id = c.id
        JOIN users u ON c.teacher_id = u.id 
        JOIN student_classes sc ON e.class_id = sc.class_id
        WHERE sc.student_id = ? AND e.start_time <= ? AND e.end_time >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $student_id, $current_time, $current_time);
$stmt->execute();
$result = $stmt->get_result();

$exam_info = $result->fetch_assoc();

if ($exam_info) {
    $exam_id = $exam_info['exam_id'];
    $end_time = $exam_info['end_time'];
    
    // 获取考试问题
    $sql = "SELECT q.id AS question_id, q.text, q.type
            FROM questions q
            JOIN exam_questions eq ON q.id = eq.question_id
            WHERE eq.exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();

    $questions = [];
    while ($row = $questions_result->fetch_assoc()) {
        $questions[] = $row;
    }

    $stmt->close();
    $conn->close();
} else {
    $stmt->close();
    $conn->close();
    echo "<script>setTimeout(function() {
                window.location.href = \"student_dashboard.html\";
            }, 1500);</script>";
    die("当前没有可参加的考试。");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>参加考试</title>
    <link rel="stylesheet" href="take_exam.css">
    <script>
    function startCountdown(endTime) {
        const end = new Date(endTime).getTime();
        const countdownElement = document.getElementById('countdown');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = end - now;

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

            if (distance < 0) {
                clearInterval(interval);
                countdownElement.innerHTML = "考试已结束";
            }
        }

        updateCountdown();
        const interval = setInterval(updateCountdown, 1000);
    }
    </script>
</head>

<body onload="startCountdown('<?php echo $end_time; ?>')">
    <div class="header">
        <div class="exam-info">
            <p>考试名称：<?php echo htmlspecialchars($exam_info['exam_name']); ?></p>
            <p>班级名称：<?php echo htmlspecialchars($exam_info['class_name']); ?></p>
            <p>教师姓名：<?php echo htmlspecialchars($exam_info['teacher_name']); ?></p>
            <p>考试剩余时间：<span id="countdown"></span></p>
        </div>
    </div>

    <form action="submit_exam.php" method="POST">
        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
        <?php foreach ($questions as $question) { ?>
        <div class="question">
            <p><strong>题目类型：</strong>
            <?php
            switch ($question['type']) {
                case 'single':
                    echo '单选题';
                    break;
                case 'multiple':
                    echo '多选题';
                    break;
                case 'blank':
                    echo '填空题';
                    break;
                case 'true_false':
                    echo '判断题';
                    break;
                case 'short_answer':
                    echo '简答题';
                    break;
                default:
                    echo '未知类型';
            }
            ?>
            </p>
            <p><?php echo htmlspecialchars($question['text']); ?></p>
            <input type="hidden" name="questions[]" value="<?php echo $question['question_id']; ?>">
            <input type="hidden" name="types[]" value="<?php echo $question['type']; ?>">

            <?php if ($question['type'] == 'single') { ?>
            <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="A"> A</label><br>
            <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="B"> B</label><br>
            <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="C"> C</label><br>
            <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="D"> D</label><br>
            <?php } elseif ($question['type'] == 'multiple') { ?>
            <label><input type="checkbox" name="answers[<?php echo $question['question_id']; ?>][]" value="A"> A</label><br>
            <label><input type="checkbox" name="answers[<?php echo $question['question_id']; ?>][]" value="B"> B</label><br>
            <label><input type="checkbox" name="answers[<?php echo $question['question_id']; ?>][]" value="C"> C</label><br>
            <label><input type="checkbox" name="answers[<?php echo $question['question_id']; ?>][]" value="D"> D</label><br>
            <?php } elseif ($question['type'] == 'blank') { ?>
            <input type="text" name="answers[<?php echo $question['question_id']; ?>]"><br>
            <?php } elseif ($question['type'] == 'true_false') { ?>
            <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="true"> 正确</label><br>
            <label><input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="false"> 错误</label><br>
            <?php } elseif ($question['type'] == 'short_answer') { ?>
            <textarea name="answers[<?php echo $question['question_id']; ?>]"></textarea><br>
            <?php } ?>
        </div>
        <?php } ?>
        <div class="botton">
            <button type="submit">提交答案</button>
        </div>
    </form>
</body>

</html>
