<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location:../../admin_login.php");
    exit();
}
include_once("config.php");

if (isset($_POST['submit'])) {
    $id = $row['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $department = $_POST['department'];
    $usertype = $_POST['usertype'];

    $sql = "INSERT INTO user_account (username, password, email, firstname, middlename, lastname, department, usertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtinsert = $db->prepare($sql);
    $result = $stmtinsert->execute([$username, $password, $email, $firstname, $middlename, $lastname, $department, $usertype]);

    if ($result) {
        header("Location: parent.php?msg=Student created successfully!");
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Talisay Senior High School LMS Admin</title>
    <link rel="stylesheet" type="text/css" href="assets/css/create_student.css">
    <link rel="shortcut icon" href="../../images/trace.svg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>

    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: green; color: white;">
        Parent Registration
    </nav>

    <div class="wrapper">
        <div class="container">
            <div class="text-center mb-4">
                <img src="assets/image/trace.svg" style="height: 25vh; width: 40vh; margin-top: -20px;">
            </div>

            <section class="content">
                <div class="container d-flex justify-content-center">
                    <form action="" method="post" style="width: 50vw; min-width: 300px;">
                        <div class="row" style="flex: 1; padding-bottom: 8vh;">
                            <div class="col-mb-4">
                                <div class="alert alert-danger alert-dismissible fade show" id="validationAlert"
                                    role="alert" style="display: none;">
                                    Please fill in all required fields.
                                </div>
                            </div>

                            <div></div>
                            <div class="col mb-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="email mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="firstname">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middlename">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="lastname">
                            </div>

                            <div></div>

                            <div class="col mb-5">
                                <label class="form-label">Department</label>
                                <div class="position-relative">
                                    <select name="department" id="department"
                                        class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="stem">STEM</option>
                                        <option value="humss">HUMSS</option>
                                        <option value="abm">ABM</option>
                                        <option value="mechanic">MECHANICS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col mb-5">
                                <label class="form-label">User Type</label>
                                <div class="position-relative">
                                    <select name="usertype" id="usertype"
                                        class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="teacher">Teacher</option>
                                        <option value="student">Student</option>
                                        <option value="parent">Parent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="">
                                <a href="parent.php" class="btn btn-outline-danger"
                                    style="padding: 7px; font-size: 15px; margin-right: 2vh;">Cancel</a>
                                <button type="submit" class="btn btn-outline-success" name="submit"
                                    style="padding: 7px; font-size: 15px;">Sign Up</button>
                            </div>

                        </div>
                    </form>
                </div>
            </section>
        </div>

        <div class="footer"></div>
    </div>

    <script>
        // Get the form element
        var form = document.querySelector('form');

        // Get the alert element
        var validationAlert = document.getElementById('validationAlert');

        form.addEventListener('submit', function (event) {
            // Get the input fields and text area
            var usernameInput = document.querySelector('input[name="username"]');
            var passwordInput = document.querySelector('input[name="password"]');
            var emailInput = document.querySelector('input[name="email"]');
            var firstnameInput = document.querySelector('input[name="firstname"]');
            var middlenameInput = document.querySelector('input[name="middlename"]');
            var lastnameInput = document.querySelector('input[name="lastname"]');
            var departmentDropdown = document.querySelector('select[name="department"]');
            var usertypeDropdown = document.querySelector('select[name="usertype"]');

            // Check if any required fields are empty
            if (usernameInput.value === '' || passwordInput.value === '' || emailInput.value === '' ||
                firstnameInput.value === '' || middlenameInput.value === '' || lastnameInput.value === '' ||
                departmentDropdown.value === '' || usertypeDropdown.value === '') {
                // Prevent form submission
                event.preventDefault();

                // Show the alert
                validationAlert.style.display = 'block';

                // Scroll to the top
                setTimeout(function () {
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    // Focus on the alert element
                    validationAlert.focus();
                }, 100); // Adjust the timeout value as needed
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
</body>

</html>