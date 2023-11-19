<?php
function getFullName($db, $user_id)
{
    $sql = "SELECT firstname, lastname FROM user_account WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getNewsNotifications($db)
{
    $sql = "SELECT news_id, title, type, name, date, end_date FROM news";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFriendNotifications($db, $user_id)
{
    $sql = "SELECT name, user_id, friend_id, date FROM friend WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTeacherNotifications($db, $user_id) {
    $sql = "SELECT name, user_id, teacher_id, date, 'teacher' as notification_type 
    FROM teacher WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMaterialNotifications($db, $studentFullName)
{
    $sql = "SELECT class_name, student, teacher_id, date, 'material' as notification_type 
    FROM classwork_material WHERE LOWER(student) LIKE :student";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':student', '%' . $studentFullName . '%');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuestionNotification($db, $studentFullName)
{
    $sql = "SELECT class_name, student, teacher_id, date, 'question' as notification_type 
    FROM classwork_question WHERE LOWER(student) LIKE :student";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':student', '%' . $studentFullName . '%');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAssignmentNotification($db, $studentFullName)
{
    $sql = "SELECT class_name, student, teacher_id, date, 'assignment' as notification_type 
    FROM classwork_assignment WHERE LOWER(student) LIKE :student";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':student', '%' . $studentFullName . '%');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuizNotification($db, $studentFullName)
{
    $sql = "SELECT class_name, student, teacher_id, date, 'quiz' as notification_type 
    FROM classwork_quiz WHERE LOWER(student) LIKE :student";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':student', '%' . $studentFullName . '%');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExamNotification($db, $studentFullName)
{
    $sql = "SELECT class_name, student, teacher_id, date, 'exam' as notification_type 
    FROM classwork_exam WHERE LOWER(student) LIKE :student";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':student', '%' . $studentFullName . '%');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuestionScoreNotification($db, $user_id)
{
    $sql = "SELECT qg.questionTitle, qg.date, u.firstname as studentFirstName, u.lastname as studentLastName, 
            t.firstname as teacherFirstName, t.lastname as teacherLastname, qg.score as score, 'questionGrade' as scoreNotification_type 
            FROM questiongrade qg
            JOIN user_account u ON qg.student_id = u.user_id
            JOIN user_account t ON qg.teacher_id = t.user_id
            WHERE u.user_id = :user_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAssignmentScoreNotification($db, $user_id)
{
    $sql = "SELECT ag.assignmentTitle, ag.date, u.firstname as studentFirstName, u.lastname as studentLastName, 
            t.firstname as teacherFirstName, t.lastname as teacherLastname, ag.score as score, 'assignmentGrade' as scoreNotification_type 
            FROM assignmentgrade ag
            JOIN user_account u ON ag.student_id = u.user_id
            JOIN user_account t ON ag.teacher_id = t.user_id
            WHERE u.user_id = :user_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuizScoreNotification($db, $user_id)
{
    $sql = "SELECT qz.quizTitle, qz.date, u.firstname as studentFirstName, u.lastname as studentLastName, 
            t.firstname as teacherFirstName, t.lastname as teacherLastname, qz.score as score, 'quizGrade' as scoreNotification_type 
            FROM quizgrade qz
            JOIN user_account u ON qz.student_id = u.user_id
            JOIN user_account t ON qz.teacher_id = t.user_id
            WHERE u.user_id = :user_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExamScoreNotification($db, $user_id)
{
    $sql = "SELECT eg.examTitle, eg.date, u.firstname as studentFirstName, u.lastname as studentLastName, 
    t.firstname as teacherFirstName, t.lastname as teacherLastname, eg.score as score, 'examGrade' as scoreNotification_type 
            FROM examgrade eg
            JOIN user_account u ON eg.student_id = u.user_id
            JOIN user_account t ON eg.teacher_id = t.user_id
            WHERE u.user_id = :user_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>