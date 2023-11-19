<?php
if (isset($_GET['class_id']) && isset($_GET['user_id'])) {
    $class_id = $_GET['class_id'];
    $user_id = $_GET['user_id'];
    $student_id = $_GET['student_id'];

    try {
        include("config.php");

        $sqlDeleteStudent = "DELETE FROM class_enrolled WHERE tc_id = ? AND teacher_id = ? AND student_id = ?";
        $stmtDeleteStudent = $db->prepare($sqlDeleteStudent);
        $stmtDeleteStudent->execute([$class_id, $user_id, $student_id]);
        $db = null;

        header("Location: class_people.php?user_id={$user_id}&class_id={$class_id}");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
