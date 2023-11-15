<?php
function getNewsNotifications($db) {
    $sql = "SELECT title, type, name, date FROM news";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudentNotifications($db, $user_id) {
    $sql = "SELECT name, user_id, student_id, date FROM student WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuestionNotifications($db, $user_id) {
    $sql = "SELECT user_id, title, date, question_course_status FROM student_question_course_answer WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAssignmentNotifications($db, $user_id) {
    $sql = "SELECT user_id, title, date, assignment_course_status FROM student_assignment_course_answer WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuizNotifications($db, $user_id) {
    $sql = "SELECT user_id, quizTitle, date, quiz_course_status FROM student_quiz_course_answer WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExamNotifications($db, $user_id) {
    $sql = "SELECT user_id, examTitle, date, exam_course_status FROM student_exam_course_answer WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClassroomNotifications($db, $user_id) {
    $sql = "SELECT student_firstname, class_name, date FROM class_enrolled WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>