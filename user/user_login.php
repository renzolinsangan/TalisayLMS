<!DOCTYPE html>
<html>

<head>
    <title>Talisay Senior High School LMS Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/user_login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="images/trace.svg" />
</head>

<body>
    <div class="popup js_error-popup popup--visible">
        <div class="popup__background"></div>
        <div class="popup__content">
            <h3 class="popup__content__title">
                <a href="index.php" class="close-button">
                    <i class="bi bi-arrow-left-short"></i>
                </a>
                <form action="user_info.php" method="POST">
                    <div class="logo-container"> <!-- Added a div container for the image -->
                        <img class="logo" src="images/user_login/trace.svg"><br>
                        <label class="login">Log in</label><br>
                    </div>

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

                    <label>Username</label>
                    <input type="text" name="username" class="bottom-border-input">

                    <label>Password</label>
                    <input type="password" name="password" class="bottom-border-input" style="margin-bottom: -7px;">

                    <a href="" class="forgot_password">Forgot Password?</a><br>
                    <button class="button" type="submit">Log in</button>
                </form>
            </h3>
        </div>
    </div>
</body>

</html>