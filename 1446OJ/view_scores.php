<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'student') {
    die("只有学生可以查看成绩");
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_id'])) {
    $exam_id = $_POST['exam_id'];

    // 检查考试是否已结束且简答题已评分
    $sql = "SELECT e.end_time, 
                   (SELECT COUNT(*) FROM questions q 
                    JOIN exam_questions eq ON q.id = eq.question_id 
                    LEFT JOIN submissions s ON q.id = s.question_id 
                    WHERE eq.exam_id = ? AND q.type = 'short_answer' AND s.score IS NULL) AS ungraded_count 
            FROM exams e 
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $exam_id, $exam_id);
    $stmt->execute();
    $stmt->bind_result($end_time, $ungraded_count);
    $stmt->fetch();
    $stmt->close();

    $current_time = date('Y-m-d H:i:s');

    if ($current_time < $end_time) {
        echo "<script>alert('考试尚未结束。'); window.location.href = 'view_scores.php';</script>";
        exit();
    }

    if ($ungraded_count > 0) {
        echo "<script>alert('考试成绩尚未公布。'); window.location.href = 'view_scores.php';</script>";
        exit();
    }

    // 获取考试的基本信息
    $sql = "SELECT e.name AS exam_name, c.name AS class_name, u.username AS teacher_name 
            FROM exams e
            JOIN classes c ON e.class_id = c.id
            JOIN users u ON c.teacher_id = u.id
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $stmt->bind_result($exam_name, $class_name, $teacher_name);
    $stmt->fetch();
    $stmt->close();

    // 获取考试题目和学生得分
    $sql = "SELECT q.text, q.points, s.answer, s.score 
            FROM questions q
            JOIN exam_questions eq ON q.id = eq.question_id
            LEFT JOIN submissions s ON q.id = s.question_id AND s.exam_id = ? AND s.student_id = ?
            WHERE eq.exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $exam_id, $student_id, $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    $total_score = 0;
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
        $total_score += $row['score'];
    }

    $stmt->close();
    $conn->close();
} else {
    // 获取学生参加过的所有考试
    $sql = "SELECT DISTINCT e.id, e.name 
            FROM exams e
            JOIN exam_questions eq ON e.id = eq.exam_id
            JOIN submissions s ON eq.question_id = s.question_id
            WHERE s.student_id = ? AND s.exam_id = e.id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $exams = [];
    while ($row = $result->fetch_assoc()) {
        $exams[] = $row;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看成绩</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: auto;
        overflow: hidden;
    }

    header {
        background: #35424a;
        color: #ffffff;
        padding-top: 30px;
        min-height: 70px;
        border-bottom: #e8491d 3px solid;
    }

    header a {
        color: #ffffff;
        text-decoration: none;
        text-transform: uppercase;
        font-size: 16px;
    }

    header ul {
        padding: 0;
        list-style: none;
    }

    header li {
        float: right;
        display: inline;
        padding: 0 20px 0 20px;
    }

    header .highlight,
    header .current a {
        color: #e8491d;
        font-weight: bold;
    }

    header .branding {
        float: left;
    }

    header .branding h1 {
        margin: 0;
    }

    h1,
    h2,
    h3 {
        text-align: center;
        color: #333;
    }

    .content {
        background: #fff;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }

    .form-group {
        margin: 20px 0;
        text-align: center;
    }

    .form-group select,
    .form-group button {
        padding: 10px;
        font-size: 16px;
    }

    .form-group button {
        background: #35424a;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .form-group button:hover {
        background: #e8491d;
    }

    .back-link {
        display: block;
        text-align: center;
        margin: 20px 0;
    }

    .back-link a {
        text-decoration: none;
        color: #35424a;
        font-size: 18px;
    }

    .back-link a:hover {
        color: #e8491d;
    }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="branding">
                <h1>成绩查询系统</h1>
            </div>
            <ul>
                <li><a href="student_dashboard.html">返回学生主页</a></li>
            </ul>
        </div>
    </header>
    <div class="container">
        <div class="content">
            <h1>查看成绩</h1>

            <?php if (isset($exam_id)) { ?>
            <h2>考试名称: <?php echo htmlspecialchars($exam_name); ?></h2>
            <h3>班级名称: <?php echo htmlspecialchars($class_name); ?></h3>
            <h3>教师姓名: <?php echo htmlspecialchars($teacher_name); ?></h3>
            <h3>总成绩: <?php echo $total_score; ?></h3>

            <table>
                <tr>
                    <th>题目</th>
                    <th>分值</th>
                    <th>你的答案</th>
                    <th>得分</th>
                </tr>
                <?php foreach ($questions as $question) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($question['text']); ?></td>
                    <td><?php echo $question['points']; ?></td>
                    <td><?php echo htmlspecialchars($question['answer']); ?></td>
                    <td><?php echo $question['score']; ?></td>
                </tr>
                <?php } ?>
            </table>
            <div class="back-link">
                <a href="view_scores.php">返回考试列表</a>
            </div>
            <?php } else { ?>
            <div class="form-group">
                <form action="view_scores.php" method="POST">
                    <label for="exam_id">选择一个考试查看成绩：</label>
                    <select id="exam_id" name="exam_id" required>
                        <option value="" disabled selected>请选择考试</option>
                        <?php foreach ($exams as $exam) { ?>
                        <option value="<?php echo $exam['id']; ?>"><?php echo htmlspecialchars($exam['name']); ?>
                        </option>
                        <?php } ?>
                    </select>
                    <button type="submit">查看成绩</button>
                </form>
            </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>