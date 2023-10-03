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
$password = $row['password'];
$email = $row['email'];
$firstname = $row['firstname'];
$middlename = $row['middlename'];
$lastname = $row['lastname'];
$department = $row['department'];
$usertype = $row['usertype'];

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $department = $_POST['department'];
    $usertype = $_POST['usertype'];

    $sql = "UPDATE user_account SET username='$username', password='$password', email='$email', firstname='$firstname', middlename='$middlename', lastname='$lastname', department='$department', usertype='$usertype' WHERE user_id=$id";
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
                    <form action="#" method="post" style="width: 50vw; min-width: 300px;">
                        <div class="row" style="flex: 1; padding-bottom: 8vh;">
                            <div class="col mb-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" value=<?php echo $username ?>>
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" value=<?php echo $password ?>>
                            </div>

                            <div class="email mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value=<?php echo $email ?>>
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="firstname" value=<?php echo $firstname ?>>
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middlename" value=<?php echo $middlename ?>>
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="lastname" value=<?php echo $lastname ?>>
                            </div>

                            <div></div>

                            <div class="col mb-5">
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
                                        <option value="mechanic" <?php if ($department === 'mechanic')
                                            echo 'selected'; ?>>MECHANICS</option>
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
</body>

</html>