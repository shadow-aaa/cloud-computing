<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以管理班级");
}

$teacher_id = $_SESSION['user_id'];

// 获取教师创建的所有班级及其学生
$sql = "SELECT c.id AS class_id, c.name AS class_name, u.id AS student_id, u.username AS student_name
        FROM classes c
        LEFT JOIN student_classes sc ON c.id = sc.class_id
        LEFT JOIN users u ON sc.student_id = u.id
        WHERE c.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $class_id = $row['class_id'];
    if (!isset($classes[$class_id])) {
        $classes[$class_id] = [
            'class_name' => $row['class_name'],
            'students' => []
        ];
    }
    if ($row['student_id']) {
        $classes[$class_id]['students'][] = [
            'student_id' => $row['student_id'],
            'student_name' => $row['student_name']
        ];
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>班级管理</title>
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
    background: rgb(0, 183, 255); /* 更改为新的渐变颜色 */
    color: #ffffff;
    padding: 20px 10px;
    position: relative;
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
    .question input[type="number"] {
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
    .question input[type="number"]:focus {
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
    .page-title {
    position: absolute;
    top: 30px;
    left: 200px; /* 调整标题位置 */
    font-size: 35px;
    color: rgb(0, 0, 0);
    font-weight: bold;
}

.top-right-button {
    position: absolute;
    top: 50%;
    right: 200px; /* 调整按钮位置 */
    transform: translateY(-50%);
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

.top-right-button:hover {
    background: #1e3c72; /* 鼠标悬浮时的背景色变化 */
    color: #fff;
}
    </style>
    <script>
    function removeStudent(classId, studentId) {
        if (confirm("确定要踢出该学生吗？")) {
            fetch('remove_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        class_id: classId,
                        student_id: studentId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("学生已被踢出");
                        location.reload();
                    } else {
                        alert("踢出学生失败");
                    }
                });
        }
    }

    function deleteClass(classId) {
        if (confirm("确定要删除该班级吗？")) {
            fetch('delete_class.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        class_id: classId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("班级已删除");
                        location.reload();
                    } else {
                        alert("删除班级失败");
                    }
                });
        }
    }
    </script>
</head>

<body>
    <header>
        <div class="container">
        <h1 class="page-title">班级管理页面</h1> 
        <a href="teacher_dashboard.html" class="top-right-button">返回</a> 
        </div>
    </header>

    <div class="container">
        <div class="question">
            <h1>创建新班级</h1>
            <form action="create_class.php" method="POST">
                <label for="class_name">班级名称：</label>
                <input type="text" id="class_name" name="class_name" required><br>
                <div class="button-container">
                    <button type="submit">创建班级</button>
                </div>
            </form>
        </div>

        <h2>班级管理</h2>
        <?php foreach ($classes as $class_id => $class) { ?>
        <div class="question">
            <h2><?php echo htmlspecialchars($class['class_name']); ?></h2>
            <button onclick="deleteClass(<?php echo $class_id; ?>)">删除班级</button>
            <ul>
                <?php foreach ($class['students'] as $student) { ?>
                <li>
                    <?php echo htmlspecialchars($student['student_name']); ?>
                    <button
                        onclick="removeStudent(<?php echo $class_id; ?>, <?php echo $student['student_id']; ?>)">踢出学生</button>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
    </div>
</body>

</html>