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
        $_SESSION['entered_username'] = $name; // Store the entered username
        header("Location: admin_login.php?error=Password is Required!");
        exit();
    } else {
        // Perform login validation using the submitted username and password
        $sql = "SELECT * FROM users WHERE user_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Check if the entered password matches the stored hashed password
            if (password_verify($pass, $row['password'])) {
                // Password is correct, store user ID in session
                $_SESSION['id'] = $row['admin_id'];
                $_SESSION['name'] = $row['user_name'];
                header("Location: index.php");
            } else {
                // Password is incorrect, show error message
                $_SESSION['entered_username'] = $name; // Store the entered username
                header("Location: admin_login.php?error=Incorrect Password!");
            }
            exit();
        } else {
            // Username not found in the database
            $_SESSION['entered_username'] = $name; // Store the entered username
            header("Location: admin_login.php?error=Incorrect Username or Password!");
            exit();
        }
    }
} else {
    // Redirect to the login page if no POST data is received
    header("Location: admin_login.php");
    exit();
}
?>
