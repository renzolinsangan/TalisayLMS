<?php
session_start();

include('db_conn.php');
if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];

    $sql = "DELETE FROM user_account WHERE user_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header('Location: teacher.php');
    } else {
        die("Connection Failed " . mysqli_connect_error());
    }
}

?>