<?php
session_start();
include("db_conn.php");

if (isset($_POST['submit_code'])) {
  $class_code = $_POST['class_code'];

  $sql = "SELECT * FROM section WHERE class_code=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $class_code);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['class_name'] = $row['class_name'];
    $_SESSION['section'] = $row['section'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name'] = $row['last_name'];

    header("Location:course.php");
    exit;
  } else {
    header("Location:course.php?msg=Invalid class code, please try again!");
    exit;
  }
}
?>