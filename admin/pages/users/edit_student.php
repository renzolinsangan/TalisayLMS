<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location:../../admin_login.php");
    exit();
}
include_once("db_conn.php");
$id = $_GET['updateid'];
$sql = "SELECT * FROM user_account WHERE user_id=$id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$username = $row['username'];
$email = $row['email'];
$firstname = $row['firstname'];
$middlename = $row['middlename'];
$lastname = $row['lastname'];
$department = $row['department'];
$usertype = $row['usertype'];
$address = $row['address'];
$contact = $row['contact'];
$grade_level = $row['grade_level'];
$section = $row['section'];

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $department = $_POST['department'];
    $usertype = $_POST['usertype'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $grade_level = $_POST['grade_level'];
    $section = $_POST['section'];

    $sql = "UPDATE user_account SET username='$username', email='$email', firstname='$firstname', middlename='$middlename', lastname='$lastname', department='$department', usertype='$usertype', address='$address', contact='$contact', grade_level='$grade_level', section='$section' WHERE user_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: student.php?msg=Student Updated Successfully!");
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
        Student Registration
    </nav>

    <div class="wrapper">
        <div class="container">
            <div class="text-center mb-4">
                <img src="assets/image/trace.svg" style="height: 25vh; width: 40vh; margin-top: -20px;">
            </div>

            <section class="content">
                <div class="container d-flex justify-content-center">
                    <form action="#" method="post" style="width: 50vw; min-width: 300px;">
                        <div class="row" style="flex: 1; padding-bottom: 8vh;">
                            <div class="col-mb-4">
                                <div class="alert alert-danger alert-dismissible fade show" id="validationAlert"
                                    role="alert" style="display: none;">
                                    All inputs are required. Please fill all of the fields, correctly.
                                </div>
                            </div>

                            <div></div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" value="<?php echo $username ?>">
                            </div>

                            <div class=" email mb-4">
                                <label class="form-label">House Address</label>
                                <input type="text" class="form-control" name="address" value="<?php echo $address ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $email ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="contact" id="contact"
                                    pattern="^(09|\+639)\d{9}$" maxlength="11" value="<?php echo $contact ?>">
                            </div>
                            <div class="div"></div>

                            <div class="col mb-4">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="firstname"
                                    value="<?php echo $firstname ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middlename"
                                    value="<?php echo $middlename ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="lastname" value="<?php echo $lastname ?>">
                            </div>

                            <div></div>

                            <div class="col mb-4">
                                <label class="form-label">Grade Level</label>
                                <div class="position-relative">
                                    <select name="grade_level" id="grade_level"
                                        class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="Grade 11" <?php if ($grade_level === 'Grade 11')
                                            echo 'selected'; ?>>Grade 11</option>
                                        <option value="Grade 12" <?php if ($grade_level === 'Grade 12')
                                            echo 'selected'; ?>>Grade 12</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Department</label>
                                <div class="position-relative">
                                    <select name="department" id="department"
                                        class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="stem" <?php if ($department === 'stem')
                                            echo 'selected'; ?>>STEM
                                        </option>
                                        <option value="humss" <?php if ($department === 'humss')
                                            echo 'selected'; ?>>HUMSS
                                        </option>
                                        <option value="abm" <?php if ($department === 'abm')
                                            echo 'selected'; ?>>ABM
                                        </option>
                                        <option value="tvl" <?php if ($department === 'tvl')
                                            echo 'selected'; ?>>TVL
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div></div>
                            <div class="col mb-5">
                                <label class="form-label">Section</label>
                                <input type="text" class="form-control" name="section" value="<?php echo $section ?>">
                            </div>

                            <div class="col mb-5">
                                <label class="form-label">User Type</label>
                                <div class="position-relative">
                                    <select name="usertype" id="usertype"
                                        class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="teacher" <?php if ($usertype === 'teacher')
                                            echo 'selected'; ?>>
                                            Teacher</option>
                                        <option value="student" <?php if ($usertype === 'student')
                                            echo 'selected'; ?>>
                                            Student</option>
                                        <option value="parent" <?php if ($usertype === 'parent')
                                            echo 'selected'; ?>>
                                            Parent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="">
                                <a href="student.php" class="btn btn-outline-danger"
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
        // Get the form element
        var form = document.querySelector('form');

        // Get the alert element
        var validationAlert = document.getElementById('validationAlert');

        form.addEventListener('submit', function (event) {
            // Get the input fields and text area
            var usernameInput = document.querySelector('input[name="username"]');
            var passwordInput = document.querySelector('input[name="password"]');
            var emailInput = document.querySelector('input[name="email"]');
            var addressInput = document.querySelector('input[name="address"]');
            var contactInput = document.querySelector('input[name="contact"]');
            var firstnameInput = document.querySelector('input[name="firstname"]');
            var middlenameInput = document.querySelector('input[name="middlename"]');
            var lastnameInput = document.querySelector('input[name="lastname"]');
            var gradelevelDropdown = document.querySelector('select[name="grade_level"]');
            var departmentDropdown = document.querySelector('select[name="department"]');
            var sectionInput = document.querySelector('select[name="section"]');
            var usertypeDropdown = document.querySelector('select[name="usertype"]');

            // Initialize a flag to track if any field is empty
            var isEmpty = false;
            var isUsernameValid = true;

            // Check if any required fields are empty
            if (usernameInput.value === '' || passwordInput.value === '' || emailInput.value === '' ||
                addressInput.value === '' || contactInput.value === '' ||
                firstnameInput.value === '' || middlenameInput.value === '' || lastnameInput.value === '' ||
                gradelevelDropdown.value === '' || departmentDropdown.value === '' ||
                sectionInput.value === '' || usertypeDropdown.value === '') {
                // Set the isEmpty flag to true
                isEmpty = true;
            }

            if (usernameInput.value.trim() === '') {
                isEmpty = true;
                usernameInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                usernameInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (passwordInput.value === '' || passwordInput.value.length < 8) {
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

            if (gradelevelDropdown.value === '') {
                isEmpty = true;
                gradelevelDropdown.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                gradelevelDropdown.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (departmentDropdown.value === '') {
                isEmpty = true;
                departmentDropdown.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                departmentDropdown.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (sectionInput.value === '') {
                isEmpty = true;
                sectionInput.classList.add('is-invalid');
            } else {
                gradelevelDropdown.classList.remove('is-invalid');
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