<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>加入班级</title>
    <link rel="stylesheet" href="join_class.css">
</head>
<style>
input::-webkit-input-placeholder {
    color: white;
}

input::-moz-placeholder {
    color: white;
}

input:-moz-placeholder {
    color: white;
}

input:-ms-input-placeholder {
    color: white;
}
</style>

<body>
    <div class="container">
        <!-- 已经加入的班级 -->
        <div class="box">
            <h1>已经加入的班级</h1>
            <?php
                session_start();
                require 'config.php';

                $student_id = $_SESSION['user_id'];
                $sql = "SELECT c.name FROM classes c
                        JOIN student_classes sc ON c.id = sc.class_id
                        WHERE sc.student_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo '<div class="white-text">'.htmlspecialchars($row['name']).'</div>';
                }

                $stmt->close();
                $conn->close();
            ?>
        </div>

        <!-- 加入新的班级 -->
        <div class="box">
            <h1>加入新的班级</h1>
            <form action="join_class_back.php" method="POST">
                <input type="text" id="class_code" placeholder="输入你要加入的班级名称" name="class_code" required><br>
                <div class="botton">
                    <button type="submit" class="button-common">加入班级</button>
                    <button type="button" class="button-common"
                        onclick="window.location.href='student_dashboard.html'">返回</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
