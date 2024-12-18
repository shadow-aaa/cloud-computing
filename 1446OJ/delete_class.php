<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以删除班级");
}

$data = json_decode(file_get_contents('php://input'), true);
$class_id = $data['class_id'];

// 删除班级
$sql = "DELETE FROM classes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$success = $stmt->execute();

// 删除班级中的学生关系
if ($success) {
    $sql = "DELETE FROM student_classes WHERE class_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
