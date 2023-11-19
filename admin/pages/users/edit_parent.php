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
$address = $row['address'];
$email = $row['email'];
$contact = $row['contact'];
$firstname = $row['firstname'];
$middlename = $row['middlename'];
$lastname = $row['lastname'];
$children = $row['children'];
$usertype = $row['usertype'];

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $children = $_POST['children'];
    $usertype = $_POST['usertype'];

    $sql = "UPDATE user_account SET username='$username', address='$address', email='$email', 
    contact='$contact', firstname='$firstname', middlename='$middlename', lastname='$lastname', children='$children', 
    usertype='$usertype' WHERE user_id=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: parent.php?msg=Parent Updated Successfully!");
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
                    <form action="#" method="post" style="width: 50vw; min-width: 300px;">
                        <div class="row" style="flex: 1; padding-bottom: 8vh;">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" value="<?php echo $username ?>">
                            </div>

                            <div></div>

                            <div class="col mb-4">
                                <label class="form-label">House Address</label>
                                <input type="text" class="form-control" name="address" value="<?php echo $address?>">
                            </div>

                            <div></div>

                            <div class="col mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $email ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="contact" id="contact"
                                    pattern="^(09|\+639)\d{9}$" maxlength="11" value="<?php echo $contact ?>">
                            </div>

                            <div></div>

                            <div class="col mb-4">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="firstname" value="<?php echo $firstname ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middlename" value="<?php echo $middlename ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="lastname" value="<?php echo $lastname ?>">
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
                                            $childFullName = $childFirstName . ' ' . $childLastName;

                                            $selected = in_array($childFullName, explode(',', $row['children'])) ? 'selected' : '';

                                            echo '<option value="' . $childFullName . '" ' . $selected . '>' . $childFullName . '</option>';
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
                passwordInput.classList.add('is-invalid');
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