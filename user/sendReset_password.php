<?php

$sname = "localhost";
$unmae = "root";
$password = "";
$db_name = "test_db";

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
    echo "Connection failed!";
}

$email = $_POST["email"];

$checkEmailSql = "SELECT * FROM user_account WHERE email = ?";
$stmtCheckEmail = $conn->prepare($checkEmailSql);
$stmtCheckEmail->bind_param("s", $email);
$stmtCheckEmail->execute();
$resultCheckEmail = $stmtCheckEmail->get_result();

if ($resultCheckEmail->num_rows === 0) {
    header("Location: user_login.php?error=Email not registered.");
    exit();
}

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$sql = "UPDATE user_account
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($stmt->affected_rows) {

    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("talisaylms@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

    Click <a href="localhost/TLMS/user/reset_password.php?token=$token">here</a> 
    to reset your password.

    END;
    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
}

header("Location: user_login.php");
?>