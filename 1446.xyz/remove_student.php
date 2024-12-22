<?php
session_start();
require 'config.php';

if ($_SESSION['role'] != 'teacher') {
    die("只有教师可以踢出学生");
}

$data = json_decode(file_get_contents('php://input'), true);
$class_id = $data['class_id'];
$student_id = $data['student_id'];

$sql = "DELETE FROM student_classes WHERE class_id = ? AND student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $class_id, $student_id);
$success = $stmt->execute();

$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
