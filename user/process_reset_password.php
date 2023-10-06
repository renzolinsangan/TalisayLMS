<?php
$sname = "localhost";
$unmae = "root";
$password = "";
$db_name = "test_db";

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
    echo "Connection failed!";
}

$token = $_POST["token"];

$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM user_account WHERE reset_token_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be atleast 8 characters.");
}

if ($_POST["password"] !== $_POST["resetpassword"]) {
    die("Password must match.");
}

$hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);

$sql = "UPDATE user_account SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashedPassword, $user["user_id"]);
$stmt->execute();

header("Location: user_login.php");
