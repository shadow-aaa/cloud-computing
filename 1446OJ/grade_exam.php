<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以批改考试");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>考试评分</title>
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
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
    }

    header h1 {
        margin: 0;
    }

    .button-common {
        width: 150px;
        text-transform: uppercase;
        border: 3px solid #FFFFFF;
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

    .form-container {
        background: #00000080;
        padding: 20px;
        border-radius: 30px;
        width: 60%;
        margin: 20px auto;
        color: white;
    }

    .form-container h2 {
        color: white;
    }

    select,
    input[type="number"] {
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

    select:focus,
    input[type="number"]:focus {
        border-color: #66afe9;
        box-shadow: 0 0 8px rgba(102, 175, 233, 0.6);
        outline: none;
        background-color: #fff;
    }

    .question {
        border-bottom: 1px solid #ccc;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }

    .question:last-child {
        border-bottom: none;
    }

    button[type="submit"] {
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

    .back-button {
        display: block;
        margin: 20px auto;
        text-decoration: none;
        color: #fff;
        background-color: #1e3c72;
        padding: 10px;
        border-radius: 5px;
        width: 100px;
        text-align: center;
    }
    </style>
</head>

<body>
    <header>
        <h1>考试评分</h1>
        <a href="teacher_dashboard.html" class="button-common">返回</a>
    </header>

    <div class="container">
        <div class="form-container">
            <form action="grade_exam.php" method="POST">
                <label for="exam_id">选择考试:</label>
                <select id="exam_id" name="exam_id" onchange="this.form.submit()" required>
                    <option value="none" selected disabled hidden>选择考试</option>
                    <?php
                    $teacher_id = $_SESSION['user_id'];
                    $sql = "SELECT e.id, e.name FROM exams e
                            JOIN classes c ON e.class_id = c.id
                            WHERE c.teacher_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $teacher_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $selected_exam_id = isset($_POST['exam_id']) ? $_POST['exam_id'] : (isset($_GET['exam_id']) ? $_GET['exam_id'] : '');

                    while ($row = $result->fetch_assoc()) {
                        $selected = $selected_exam_id == $row['id'] ? 'selected' : '';
                        echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                    }

                    $stmt->close();
                    ?>
                </select>
            </form>
        </div>

        <?php
        if ($selected_exam_id) {
            $sql = "SELECT s.id as submission_id, u.username, q.text, q.type, s.answer, s.score
                    FROM submissions s
                    JOIN questions q ON s.question_id = q.id
                    JOIN users u ON s.student_id = u.id
                    WHERE s.exam_id = ? AND q.type = 'short_answer'
                    ORDER BY u.username, s.id";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $selected_exam_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $current_student = null;
            echo '<form action="grade_exam_back.php" method="POST">';
            echo '<input type="hidden" name="exam_id" value="' . $selected_exam_id . '">';

            while ($row = $result->fetch_assoc()) {
                if ($current_student !== $row['username']) {
                    if ($current_student !== null) {
                        echo "</div>";
                    }
                    $current_student = $row['username'];
                    echo "<div class='form-container'>";
                    echo "<h2>学生: " . htmlspecialchars($current_student) . "</h2>";
                    echo "<div class='student-submissions'>";
                }

                echo "<div class='question'>";
                echo "<p>题目: " . htmlspecialchars($row['text']) . "</p>";
                echo "<p>学生答案: " . htmlspecialchars($row['answer']) . "</p>";
                echo "<label for='score-" . $row['submission_id'] . "'>得分：</label>";
                echo "<input type='number' id='score-" . $row['submission_id'] . "' name='scores[" . $row['submission_id'] . "]' value='" . $row['score'] . "'>";
                echo "</div>";
            }
            if ($current_student !== null) {
                echo "</div>";
            }

            echo "<button type='submit'>提交评分</button>";
            echo "</form>";

            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</body>

</html>