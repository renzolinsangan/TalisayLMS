<?php
$sname = "localhost";
$unmae = "root";
$password = "";
$db_name = "test_db";

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
    echo "Connection failed!";
}

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM user_account WHERE reset_token_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

$errors = []; // Create an array to store validation errors

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"];
    $resetpassword = $_POST["resetpassword"];

    if (empty($password)) {
        $errors[] = "Please input a password.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $resetpassword) {
        $errors[] = "Passwords must match.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE user_account SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $user["user_id"]);
        $stmt->execute();

        header("Location: user_login.php");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Talisay Senior High School LMS</title>
    <link rel="stylesheet" href="css/login_user.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="shortcut icon" href="images/trace.svg" />
</head>

<body>

    <div class="wrapper">
        <div class="container main">
            <form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 side-image">
                    </div>
                    <div class="col-md-6 right">
                        <div class="input-box">
                            <img src="images/user_login/trace.svg" alt="">
                            <header>Reset Password</header>
                            <?php
                            // Display validation errors if any
                            if (!empty($errors)) {
                                echo '<div class="alert alert-danger" role="alert" style="display: flex; text-align: left; align-items: center; height: 8vh;">';
                                foreach ($errors as $error) {
                                    echo '<p class="mt-3">' . htmlspecialchars($error) . '</p>';
                                }
                                echo '</div>';
                            }
                            ?>
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <div class="input-field">
                                <input type="password" class="input" name="password" id="password">
                                <label for="password">New Password</i></label>
                            </div>
                            <div class="input-field">
                                <input type="password" class="input" id="resetpassword" name="resetpassword">
                                <label for="resetpassword">Repeat Password</i></label>
                            </div>
                            <div class="input-field">
                                <button type="submit" class="submit">Log in</button>
                            </div>
                            <div class="forgot">
                                <span>If you wish to cancel, press <a href="user_login.php">here.</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>