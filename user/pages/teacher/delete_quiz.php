<?php
session_start();
include("db_conn.php");

if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
}

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];

    $sql = "DELETE FROM classwork_quiz WHERE quiz_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: class_classwork.php?class_id=$class_id");
    } else {
        die("Connection Failed " . mysqli_connect_error());
    }
}
?>