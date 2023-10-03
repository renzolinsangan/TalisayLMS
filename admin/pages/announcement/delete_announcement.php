<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location:../../admin_login.php");
    exit();
}
include('db_conn.php');
if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];

    $sql = "DELETE FROM news WHERE news_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header('Location: announcement.php');
    } else {
        die("Connection Failed " . mysqli_connect_error());
    }
}

?>