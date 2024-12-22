<?php
// 连接数据库
$servername = "localhost";
$username = "root";
$password = "352713716";
$dbname = "exam_system";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 查询题目
$sql = "SELECT id, text, type, answer, points FROM questions";
$result = $conn->query($sql);

$questions = array();

// 获取数据并存储到数组中
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}

// 关闭数据库连接
$conn->close();

// 返回 JSON 格式的题目数据
echo json_encode($questions);
?>
