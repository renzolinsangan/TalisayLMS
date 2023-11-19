<?php
function getNewsNotifications($db) {
    $sql = "SELECT news_id, title, type, name, date, end_date FROM news";
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
    $sql = "SELECT user_id, question_id, title, date, question_course_status FROM student_question_course_answer 
    WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAssignmentNotifications($db, $user_id) {
    $sql = "SELECT user_id, assignment_id, title, date, assignment_course_status FROM student_assignment_course_answer 
    WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuizNotifications($db, $user_id) {
    $sql = "SELECT user_id, quiz_id, quizTitle, date, quiz_course_status FROM student_quiz_course_answer 
    WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExamNotifications($db, $user_id) {
    $sql = "SELECT user_id, exam_id, examTitle, date, exam_course_status FROM student_exam_course_answer WHERE teacher_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClassroomNotifications($db, $user_id) {
    $sql = "SELECT student_firstname, tc_id, class_name, date FROM class_enrolled WHERE teacher_id = :user_id AND archive_status = ''";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>