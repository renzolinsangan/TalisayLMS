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
        header("Location: user_login.php?error=Username is Required!");
        exit();
    } else if (empty($pass)) {
        $_SESSION['entered_username'] = $name; // Store the entered username
        header("Location: user_login.php?error=Password is Required!");
        exit();
    } else {
        // Fetch the user data from the database based on the username
        $sql = "SELECT * FROM user_account WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Check if the stored password has a specific prefix indicating it's hashed
            if (password_verify($pass, $row['password'])) {
                // Password is either hashed and verified or plaintext, proceed with login
                // Set the usertype and first name/last name in the session variables
                $_SESSION['usertype'] = $row['usertype'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['first_name'] = $row['firstname'];
                $_SESSION['last_name'] = $row['lastname'];

                // Close the connection
                $stmt->close();
                $conn->close();

                // Redirect to the appropriate page based on the user's usertype
                if ($_SESSION['usertype'] === "teacher") {
                    header("Location: pages/teacher/index.php");
                } elseif ($_SESSION['usertype'] === "student") {
                    header("Location: pages/student/index.php");
                } elseif ($_SESSION['usertype'] === "parent") {
                    header("Location: pages/teacher/index.php");
                } else {
                    header("Location: user_login.php?error=Invalid usertype");
                }
                exit();
            } 
            else if (($pass === $row['password'])){
                $_SESSION['usertype'] = $row['usertype'];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['first_name'] = $row['firstname'];
                $_SESSION['last_name'] = $row['lastname'];

                // Close the connection
                $stmt->close();
                $conn->close();

                // Redirect to the appropriate page based on the user's usertype
                if ($_SESSION['usertype'] === "teacher") {
                    header("Location: pages/teacher/index.php");
                } elseif ($_SESSION['usertype'] === "student") {
                    header("Location: pages/student/index.php");
                } elseif ($_SESSION['usertype'] === "parent") {
                    header("Location: pages/teacher/index.php");
                } else {
                    header("Location: user_login.php?error=Invalid usertype");
                }
                exit();
            } else {
                // Passwords don't match, handle login failure
                $_SESSION['entered_username'] = $name; // Store the entered username
                header("Location: user_login.php?error=Incorrect Password!");
                exit();
            }
        } else {
            // User not found, handle login failure
            header("Location: user_login.php?error=Incorrect Username or Password!");
            exit();
        }
    }
} else {
    header("Location: user_login.php");
    exit();
}
?>