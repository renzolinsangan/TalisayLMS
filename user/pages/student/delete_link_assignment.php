<?php
session_start();
include("db_conn.php");

if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
}

if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];
}

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];

    $sql = "DELETE FROM assignment_course_upload WHERE assignment_course_upload_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: assignment_course.php?class_id=$class_id&assignment_id=$assignment_id");
    } else {
        die("Connection Failed " . mysqli_connect_error());
    }
}
?>