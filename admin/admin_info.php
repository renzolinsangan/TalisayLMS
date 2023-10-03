<?php
session_start();
include "db_conn.php";

if (isset($_POST['username']) && isset($_POST['password'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $name = validate($_POST['username']);
    $pass = validate($_POST['password']);

    if (empty($name)) {
        header("Location: admin_login.php?error=Username is Required!");
        exit();
    } else if (empty($pass)) {
        header("Location: admin_login.php?error=Password is Required!");
        exit();
    } else {
        // Perform login validation using the submitted username and password
        $sql = "SELECT * FROM users WHERE user_name = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $pass);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Close the connection
            $stmt->close();
            $conn->close();

            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['user_name'];

            if ($_SESSION['name'] === "principal" || $_SESSION['name'] === "guidance") {
                header("Location: index.php");
            } else {
                header("Location: admin_login.php?error=Incorrect Username or Password!");
            }

            exit();
        } else {
            header("Location: admin_login.php?error=Incorrect Username or Password!");
            exit();
        }
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>