<?php
session_start();
include("db_conn.php");

if(isset($_POST['register']))
{
    $username=$_POST['username'];
    $password=$_POST['password'];
    $hashedPassword=password_hash($password, PASSWORD_DEFAULT);
    $email=$_POST['email'];

    $sql="INSERT INTO users (user_name, password, email) VALUES (?, ?, ?)";
    $stmtinsert=$conn->prepare($sql);
    $result=$stmtinsert->execute([$username, $hashedPassword, $email]);
    
    if($result)
    {
        header("Location: admin_login.php?msg='Admin Registered Successfully!'");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="shortcut icon" href="images/trace.svg" />
    <title>Login</title>
</head>

<body>
    <div class="wrapper">
        <div class="container main">
            <form action="admin_info.php" method="POST">
                <div class="row">
                    <div class="col-md-6 side-image">
                    </div>
                    <div class="col-md-6 right">
                        <div class="input-box">
                            <img src="images/trace.svg" alt="">
                            <header>Admin Log In</header>
                            <?php
                            if (isset($_GET['error'])) {
                                ?>
                                <p class="error">
                                    <i class="bi bi-exclamation-triangle-fill" style="margin-right: 5px;"></i>
                                    <?php echo $_GET['error']; ?>
                                </p>
                                <?php
                            }
                            ?>
                            <div class="input-field" style="margin-top: 20px;">
                                <input type="text" class="input" name="username" id="username" <?php echo (isset($_GET['error']) && strpos($_GET['error'], 'Password') !== false) ? 'value="' . $_SESSION['entered_username'] . '"' : ''; ?>>
                                <label for="username">Username</label>
                            </div>
                            <div class="input-field">
                                <input type="password" class="input" name="password" id="pass">
                                <label for="pass">Password</label>
                            </div>
                            <div class="input-field">
                                <button type="submit" class="submit">Log in</button>
                            </div>
                            <div class="forgot">
                                <span>If you don't have account, <a href="#" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop">Register</a> here.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form action="#" method="post">
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h2>Register Admin</h2>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="register" class="btn btn-success">Send</button>
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
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the error message from the query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get("error");

        // Display the error message in the modal if it's present
        if (error) {
            const errorMessageDiv = document.querySelector(".error-message");
            errorMessageDiv.textContent = error;
        }
    });
</script>
</body>

</html>