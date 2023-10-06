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
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $department = $_POST['department'];
    $usertype = $_POST['usertype'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];

    $sql = "INSERT INTO user_account (username, password, email, firstname, middlename, lastname, department, usertype, address, contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtinsert = $db->prepare($sql);
    $result = $stmtinsert->execute([$username, $hashedPassword, $email, $firstname, $middlename, $lastname, $department, $usertype, $address, $contact]);

    if ($result) {
        header("Location: teacher.php?msg=Teacher created successfully!");
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
        Teacher Registration
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
                                    All inputs are required. Please fill all of the fields, correctly.
                                </div>
                            </div>

                            <div></div>
                            <div class="col mb-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Password (8 characters)</label>
                                <input type="password" class="form-control" name="password" maxlength="16">
                            </div>

                            <div class="email mb-4">
                                <label class="form-label">House Address</label>
                                <input type="text" class="form-control" name="address">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="contact" id="contact"
                                    pattern="^(09|\+639)\d{9}$" maxlength="11">
                            </div>
                            <div class="div"></div>

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
                                        <option value="tvl">TVL</option>
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
                                <a href="teacher.php" class="btn btn-outline-danger"
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
        // Get the input element by its ID
        var contactInput = document.getElementById('contact');

        // Add an input event listener to the input field
        contactInput.addEventListener('input', function (e) {
            // Get the input value and remove any non-numeric characters
            var inputValue = e.target.value.replace(/\D/g, '');

            // Update the input field with the cleaned numeric value
            e.target.value = inputValue;
        });
    </script>
    <script>
        var form = document.querySelector('form');
        var validationAlert = document.getElementById('validationAlert');

        form.addEventListener('submit', function (event) {
            var usernameInput = document.querySelector('input[name="username"]');
            var passwordInput = document.querySelector('input[name="password"]');
            var emailInput = document.querySelector('input[name="email"]');
            var addressInput = document.querySelector('input[name="address"]');
            var contactInput = document.querySelector('input[name="contact"]');
            var firstnameInput = document.querySelector('input[name="firstname"]');
            var middlenameInput = document.querySelector('input[name="middlename"]');
            var lastnameInput = document.querySelector('input[name="lastname"]');
            var departmentDropdown = document.querySelector('select[name="department"]');
            var usertypeDropdown = document.querySelector('select[name="usertype"]');

            // Initialize a flag to track if any field is empty
            var isEmpty = false;
            var isUsernameValid = true;

            // Check if any required fields are empty
            if (usernameInput.value === '' || passwordInput.value === '' || emailInput.value === '' ||
                addressInput.value === '' || contactInput.value === '' ||
                firstnameInput.value === '' || middlenameInput.value === '' || lastnameInput.value === '' ||
                departmentDropdown.value === '' || usertypeDropdown.value === '') {
                // Set the isEmpty flag to true
                isEmpty = true;
            }

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

            // Check if the password consists of all numbers
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

            if (addressInput.value.trim() === '') {
                isEmpty = true;
                addressInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                addressInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            // Check if the contact number is not exactly 11 digits or doesn't start with "09"
            if (contactInput.value.length !== 11 || !contactInput.value.startsWith("09")) {
                // Set the isEmpty flag to true
                isEmpty = true;
                contactInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                contactInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            var namePattern = /^[A-Za-z\s]+$/;

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

            if (departmentDropdown.value === '') {
                isEmpty = true;
                departmentDropdown.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                departmentDropdown.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (usertypeDropdown.value === '') {
                isEmpty = true;
                usertypeDropdown.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                usertypeDropdown.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            // Check if any field is empty or the contact number is invalid
            if (isEmpty) {
                // Prevent form submission
                event.preventDefault();

                // Show the validation alert
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