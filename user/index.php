<?php
include("db_conn.php");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Talisay Senior High School LMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/homepage.css">
    <link rel="shortcut icon" href="images/trace.svg" />
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="images/trace.svg" alt="Bootstrap" width="75" height="60">
                <span class="desktop-text">Talisay Senior High School</span>
            </a>

            <a href="user_login.php"><button type="button" class="btn btn-outline-success">Log in</button></a>
        </div>
    </nav>

    <!-- main -->
    <section class="main"></section>

    <!-- TSHS School Tracks -->
    <section class="track">
        <div class="container py-2">
            <div class="row py-5">
                <div class="col-lg-5 m-auto text-center">
                    <h1>Talisay SHS</h1>
                    <h5 style="color: green; font-weight: normal;">School Tracks</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <img src="images/homepage/stem.png" class="img-fluid" alt="">
                        </div>
                    </div>
                    <h4>STEM</h4>
                    <p>Science, Technology, Engineering and Science</p>
                </div>
                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <img src="images/homepage/humss.png" class="img-fluid" alt="">
                        </div>
                    </div>
                    <h4>HUMSS</h4>
                    <p>Humanities and Social Sciences</p>
                </div>
                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <img src="images/homepage/abm.png" class="img-fluid" alt="">
                        </div>
                    </div>
                    <h4>ABM</h4>
                    <p>Accountancy, Business and Management</p>
                </div>
                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <img src="images/homepage/mechanic.png" class="img-fluid" alt="">
                        </div>
                    </div>
                    <h4>TVL</h4>
                    <p>Technical-Vocational-Livelihood</p>
                </div>
            </div>
        </div>

    </section>

    <!-- Registered Section -->
    <section class="count">
        <div class="container">
            <div class="row py-5">
                <div class="col-lg-3 m-auto mt-5 text-center">
                    <?php
                    $dash_category_query = "SELECT * FROM user_account WHERE usertype = 'teacher'";
                    $dash_category_query_run = mysqli_query($conn, $dash_category_query);

                    if ($category_total = mysqli_num_rows($dash_category_query_run)) {
                        echo '<h1> ' . $category_total . ' </h1>';
                    } else {
                        echo '<h1>No Data</h1>';
                    }
                    ?>
                    <h5 style="color: green; font-weight: bold;">Registered Teacher</h5>
                </div>
                <div class="col-lg-3 m-auto mt-5 text-center">
                    <?php
                    $dash_category_query = "SELECT * FROM user_account WHERE usertype = 'student'";
                    $dash_category_query_run = mysqli_query($conn, $dash_category_query);

                    if ($category_total = mysqli_num_rows($dash_category_query_run)) {
                        echo '<h1> ' . $category_total . ' </h1>';
                    } else {
                        echo '<h1>0</h1>';
                    }
                    ?>
                    <h5 style="color: green; font-weight: bold;">Registered Student</h5>
                </div>
                <div class="col-lg-3 m-auto mt-5 text-center">
                    <?php
                    $dash_category_query = "SELECT * FROM user_account WHERE usertype = 'parent'";
                    $dash_category_query_run = mysqli_query($conn, $dash_category_query);

                    if ($category_total = mysqli_num_rows($dash_category_query_run)) {
                        echo '<h1> ' . $category_total . ' </h1>';
                    } else {
                        echo '<h1>0</h1>';
                    }
                    ?>
                    <h5 style="color: green; font-weight: bold;">Registered Parent</h5>
                </div>
            </div>
        </div>
    </section>

    <section class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-1" style="margin-right: -53px; margin-left: 185vh;">
                    <a class="circle-icon" href="https://www.facebook.com/DepEdTayoTalisaySHS342218" target="_blank">
                        <i class="bi bi-facebook"></i>
                    </a>
                </div>
                <div class="col">
                    <a class="circle-icon" href="https://www.youtube.com/watch?v=oNGrxTSJoFo&t=39s" target="_blank">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function () {
            $('#teacher_button').click(function () {
                var username = $('#username').val();
                var password = $('#password').val();

                if (username != '' && password != '') {

                }
                else {
                    alert("Both Fields are Required!");
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
</body>

</html>