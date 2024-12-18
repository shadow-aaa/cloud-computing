<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以创建考试");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exam_name = $_POST['exam_name'];
    $class_ids = $_POST['class_ids'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $question_ids = $_POST['question_ids'];

    foreach ($class_ids as $class_id) {
        // 检查是否已有考试在同一时间段内
        $sql = "SELECT COUNT(*) FROM exams WHERE class_id = ? AND ((start_time <= ? AND end_time >= ?) OR (start_time <= ? AND end_time >= ?))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $class_id, $start_time, $start_time, $end_time, $end_time);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "<script>alert('同一个班级在同一个时间段不能创建多个考试');</script>";
            continue;
        } else {
            echo "<script>alert('考试创建成功');
            setTimeout(function() {
                window.location.href = \"teacher_dashboard.html\";
            }, 1500);</script>";
        }

        $sql = "INSERT INTO exams (name, class_id, start_time, end_time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siss", $exam_name, $class_id, $start_time, $end_time);
        $stmt->execute();
        $exam_id = $stmt->insert_id;

        foreach ($question_ids as $question_id) {
            $sql = "INSERT INTO exam_questions (exam_id, question_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $exam_id, $question_id);
            $stmt->execute();
        }
    }
    ?>
<?php
}

$teacher_id = $_SESSION['user_id'];

$sql = "SELECT id, name FROM classes WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes_result = $stmt->get_result();

$sql = "SELECT id, text, type, points FROM questions";
$questions_result = $conn->query($sql);

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创建考试</title>
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

    header {
        background: linear-gradient(to right, #1e3c72, #2a5298);
        color: #ffffff;
        padding-top: 30px;
        min-height: 70px;
        border-bottom: #e8491d 3px solid;
    }

    header h1 {
        margin: 0;
        text-align: center;
    }

    .question {
        border: 1px solid white;
        width: 60%;
        text-align: center;
        margin: 20px auto;
        background: #00000080;
        padding: 20px 50px;
        border-radius: 30px;
        transition: 0.2s;
    }

    .question button {
        width: 150px;
        text-transform: uppercase;
        border: 3px solid #FFFFFF;
        margin-top: 18px;
        text-align: center;
        font-size: 18px;
        color: #FFFFFF;
        line-height: 50px;
        border-radius: 30px;
        cursor: pointer;
        transition: 0.2s;
        background: rgba(0, 0, 0, 0);
        display: inline-block;
    }

    .question h1,
    .question h2,
    .question label,
    .question p {
        color: #FFFFFF;
    }

    .question textarea,
    .question select,
    .question input[type="text"],
    .question input[type="number"],
    .question input[type="datetime-local"] {
        width: 80%;
        padding: 10px;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        font-size: 16px;
        color: #333;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    .question textarea:focus,
    .question select:focus,
    .question input[type="text"]:focus,
    .question input[type="number"]:focus,
    .question input[type="datetime-local"]:focus {
        border-color: #66afe9;
        box-shadow: 0 0 8px rgba(102, 175, 233, 0.6);
        outline: none;
        background-color: #fff;
    }

    .question ul {
        list-style: none;
        padding: 0;
    }

    .question li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        color: #fff;
    }

    .question li button {
        margin-left: 20px;
    }

    .button-common {
        width: 150px;
        text-transform: uppercase;
        border: 3px solid #FFFFFF;
        margin-top: 18px;
        text-align: center;
        font-size: 18px;
        color: #FFFFFF;
        line-height: 50px;
        border-radius: 30px;
        cursor: pointer;
        transition: 0.2s;
        background: rgba(0, 0, 0, 0);
        display: inline-block;
    }

    .button-container {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .multi-select {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        max-width: 400px;
        margin: 0 auto 20px;
    }

    .multi-select label {
        display: block;
        margin: 5px 0;
        color: #fff;
    }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1>创建考试页面</h1><br>
            <a href="teacher_dashboard.html" class="button-common" style="margin-bottom: 20px;">返回</a>
        </div>
    </header>

    <div class="container">
        <div class="question">
            <h1>创建考试</h1>
            <form action="create_exam.php" method="POST">
                <label for="exam_name">考试名称：</label>
                <input type="text" id="exam_name" name="exam_name" required><br>

                <label for="class_ids">选择班级：</label>
                <div class="multi-select">
                    <?php while ($row = $classes_result->fetch_assoc()) { ?>
                    <label>
                        <input type="checkbox" id="class_<?php echo $row['id']; ?>" name="class_ids[]"
                            value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['name']); ?>
                    </label>
                    <?php } ?>
                </div>

                <label for="start_time">开始时间：</label>
                <input type="datetime-local" id="start_time" name="start_time" required><br>

                <label for="end_time">结束时间：</label>
                <input type="datetime-local" id="end_time" name="end_time" required><br>

                <label for="question_ids">选择题目：</label>
                <div class="multi-select">
                    <?php while ($row = $questions_result->fetch_assoc()) { ?>
                    <label>
                        <input type="checkbox" id="question_<?php echo $row['id']; ?>" name="question_ids[]"
                            value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['text']); ?> (
                        <?php
                                switch ($row['type']) {
                                    case 'single':
                                        echo htmlspecialchars("单选题");
                                        break;
                                    case 'blank':
                                        echo htmlspecialchars("填空题");
                                        break;
                                    case 'short_answer':
                                        echo htmlspecialchars("简答题");
                                        break;
                                    case 'true_false':
                                        echo htmlspecialchars("判断题");
                                        break;
                                    default:
                                        echo htmlspecialchars("多选题");
                                        break;
                                }
                            ?> - <?php echo htmlspecialchars($row['points']) . " 分"; ?> )
                    </label>
                    <?php } ?>
                </div>
                <button type="submit" class="button-common">创建考试</button>
            </form>

            <h2>当前已有考试</h2>
            <?php
            $sql = "SELECT e.name, e.start_time, e.end_time, c.name as class_name 
                    FROM exams e
                    JOIN classes c ON e.class_id = c.id
                    WHERE c.teacher_id = ? AND e.start_time <= NOW() AND e.end_time >= NOW()";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $teacher_id);
            $stmt->execute();
            $current_exams_result = $stmt->get_result();

            while ($row = $current_exams_result->fetch_assoc()) {
                echo "<p>考试名称：" . htmlspecialchars($row['name']) . " | 班级：" . htmlspecialchars($row['class_name']) . " | 开始时间：" . $row['start_time'] . " | 结束时间：" . $row['end_time'] . "</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</body>

</html>