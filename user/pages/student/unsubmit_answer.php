<?php
session_start();
include("config.php");

if (isset($_SESSION['user_id'])) {
    $class_id = $_POST['class_id'];
    $question_id = $_POST['question_id'];
    $new_answer = $_POST['new_answer'];

    // Check if the user has already submitted an answer
    $sql_check_submission = "SELECT * FROM student_question_course_answer WHERE class_id=? AND question_id=?";
    $stmt_check_submission = $db->prepare($sql_check_submission);
    $stmt_check_submission->execute([$class_id, $question_id]);
    $submission_data = $stmt_check_submission->fetch(PDO::FETCH_ASSOC);

    if ($submission_data) {
        // User has submitted an answer, so update it
        $sql_update_submission = "UPDATE student_question_course_answer SET question_answer=? WHERE class_id=? AND question_id=?";
        $stmt_update_submission = $db->prepare($sql_update_submission);
        $stmt_update_submission->execute([$new_answer, $class_id, $question_id]);

        // You can perform additional actions here if needed

        echo "Updated";
    } else {
        // User has not submitted an answer, nothing to update
        echo "NoSubmission";
    }
} else {
    echo "Unauthorized"; // Handle unauthorized access
}
?>
