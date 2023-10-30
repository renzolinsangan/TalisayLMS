<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location:../../admin_login.php");
    exit();
}
include_once("config.php");

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $children = $_POST['children'];
    $usertype = $_POST['usertype'];

    $sql = "INSERT INTO parent_account (username, password, address, email, contact, firstname, middlename, lastname, children, usertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtinsert = $db->prepare($sql);
    $result = $stmtinsert->execute([$username, $hashedPassword, $address, $email, $contact, $firstname, $middlename, $lastname, $children, $usertype]);

    if ($result) {
        header("Location: parent.php?msg=Parent created successfully!");
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
    <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/create_student.css">
    <link rel="shortcut icon" href="../../images/trace.svg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script type="text/javascript" src="multi-select"></script>
    <script type="text/javascript" src="js/virtual-select.min.js"></script>
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

                            <div></div>

                            <div class="col mb-4">
                                <label class="form-label">House Address</label>
                                <input type="text" class="form-control" name="address">
                            </div>

                            <div></div>

                            <div class="col mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="contact" id="contact"
                                    pattern="^(09|\+639)\d{9}$" maxlength="11">
                            </div>

                            <div></div>

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
                                <label class="form-label">Children</label>
                                <div class="position-relative">
                                    <select id="multipleSelect" multiple name="children"
                                        placeholder="Select Parent's Child" data-search="true"
                                        data-silent-initial-value-set="true" style="height: 45px;">
                                        <?php
                                        include("db_conn.php");

                                        $sql_children = "SELECT * FROM user_account WHERE usertype = 'student'";
                                        $childrenResult = mysqli_query($conn, $sql_children);

                                        while ($childrenRow = mysqli_fetch_assoc($childrenResult)) {
                                            $childFirstName = $childrenRow['firstname'];
                                            $childLastName = $childrenRow['lastname'];
                                            echo '<option value="' . $childFirstName . ' ' . $childLastName . '">' . $childFirstName . ' ' . $childLastName . '</option>';
                                        }
                                        ?>
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
            var addressInput = document.querySelector('input[name="address"]');
            var emailInput = document.querySelector('input[name="email"]');
            var contactInput = document.querySelector('input[name="contact"]');
            var firstnameInput = document.querySelector('input[name="firstname"]');
            var middlenameInput = document.querySelector('input[name="middlename"]');
            var lastnameInput = document.querySelector('input[name="lastname"]');
            var childrenDropdown = document.querySelector('select[name="children"]');
            var usertypeDropdown = document.querySelector('select[name="usertype"]');

            if (usernameInput.value.trim() === '') {
                isEmpty = true;
                usernameInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                usernameInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (passwordInput.value === '' || passwordInput.value.length < 8 || passwordInput.value.length > 100) {
                // Set the isEmpty flag to true
                isEmpty = true;
                passwordInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                passwordInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (addressInput.value.trim() === '') {
                isEmpty = true;
                addressInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                addressInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            var allNumbersPattern = /^[0-9]+$/;
            if (allNumbersPattern.test(passwordInput.value)) {
                // Set the isEmpty flag to true
                isEmpty = true;
                passwordInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            }

            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if (!emailPattern.test(emailInput.value)) {
                // Set the isEmpty flag to true
                isEmpty = true;
                emailInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                emailInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (contactInput.value.length !== 11 || !contactInput.value.startsWith("09")) {
                // Set the isEmpty flag to true
                isEmpty = true;
                contactInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                contactInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            var namePattern = /^[A-Za-z\s.]+$/;

            if (!namePattern.test(firstnameInput.value)) {
                // Set the isEmpty flag to true
                isEmpty = true;
                firstnameInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                firstnameInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (!namePattern.test(middlenameInput.value)) {
                // Set the isEmpty flag to true
                isEmpty = true;
                middlenameInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                middlenameInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (!namePattern.test(lastnameInput.value)) {
                // Set the isEmpty flag to true
                isEmpty = true;
                lastnameInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                lastnameInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (usertypeDropdown.value === '') {
                isEmpty = true;
                usertypeDropdown.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                usertypeDropdown.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            // Check if any required fields are empty
            if (usernameInput.value === '' || passwordInput.value === '' || addressInput.value === '' ||
                emailInput.value === '' || contactInput.value === '' ||
                firstnameInput.value === '' || middlenameInput.value === '' || lastnameInput.value === '' ||
                childrenDropdown.value === '' || usertypeDropdown.value === '') {
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
    <script type="text/javascript" src="js/virtual-select.min.js"></script>
    <script>
        VirtualSelect.init({
            ele: '#multipleSelect'
        });

        VirtualSelect.init({
            ele: '#classTopicSelect'
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