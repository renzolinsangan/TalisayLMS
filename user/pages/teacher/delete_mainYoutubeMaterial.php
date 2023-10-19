<?php
session_start();
include("db_conn.php");

if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
}

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];

    $sql = "DELETE FROM classwork_material_upload WHERE material_upload_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: classwork_material.php?class_id=$class_id");
    } else {
        die("Connection Failed " . mysqli_connect_error());
    }
}
?>