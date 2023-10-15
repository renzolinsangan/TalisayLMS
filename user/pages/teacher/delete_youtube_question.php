<?php
session_start();
include("db_conn.php");

if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
}

if (isset($_GET['updateid'])) {
    $update_id = $_GET['updateid'];
}

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];
    $updateid = $update_id;

    $sql = "DELETE FROM classwork_question_upload WHERE question_upload_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: edit_question.php?updateid=$updateid&class_id=$class_id");
    } else {
        die("Connection Failed " . mysqli_connect_error());
    }
}
?>