<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: admin_login.php");
  exit();
}

include("db_conn.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/notification.css">
  <link rel="shortcut icon" href="images/trace.svg" />
</head>

<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="images/trace.svg" class="mr-2"
            alt="logo" />Talisay LMS</a>
        <a class="navbar-brand brand-logo-mini" href="index.php"><img src="images/trace.svg" alt="logo" /></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
              data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <?php
            include("config.php");

            $sqlFeedbackNotif = "SELECT firstname, lastname, date FROM feedback ORDER BY date DESC";
            $resultFeedbackNotif = $db->query($sqlFeedbackNotif);

            $sqlNews = "SELECT type, title, end_date FROM news";
            $resultNews = $db->query($sqlNews);
            $currentDate = date('Y-m-d');
            ?>

            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
              aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>

              <?php
              while ($row = $resultFeedbackNotif->fetch(PDO::FETCH_ASSOC)) {
                $fullName = $row['firstname'] . ' ' . $row['lastname'];
                $submissionDate = $row['date'];
                ?>
                <a href="pages/feedback/feedback.php"class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                      <i class="ti-info-alt mx-0"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">
                      <?php echo $fullName; ?> has sent a feedback
                    </h6>
                    <p class="font-weight-light small-text mb-0 text-muted">
                      <?php echo date('F j', strtotime($submissionDate)); ?>
                    </p>
                  </div>
                </a>
              <?php } ?>

              <?php
              while ($newsRow = $resultNews->fetch(PDO::FETCH_ASSOC)) {
                $type = $newsRow['type'];
                $title = $newsRow['title'];
                $endDate = $newsRow['end_date'];

                if ($currentDate > $endDate) {
                  ?>
                  <a href="pages/announcement/announcement.php" class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-danger">
                        <i class="ti-alarm-clock mx-0"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <h6 class="preview-subject font-weight-normal">
                        <?php echo ucfirst($type); ?> -
                        <?php echo $title; ?> has expired
                      </h6>
                      <p class="font-weight-light small-text mb-0 text-muted">
                        <?php echo date('F j', strtotime($endDate)); ?>
                      </p>
                    </div>
                  </a>
                <?php }
              } ?>
            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="images/faces/profile.png" alt="profile" />
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a href="admin_logout.php" class="dropdown-item">
                <i class="ti-power-off text-success"></i>
                Logout
              </a>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
          data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <div class="container-fluid page-body-wrapper">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item mb-3">
            <a class="nav-link" href="index.php">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" href="pages/announcement/announcement.php">
              <i class="menu-icon"><i class="bi bi-megaphone"></i></i>
              <span class="menu-title">Announcement</span>
            </a>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false"
              aria-controls="form-elements">
              <i class="menu-icon"><i class="bi bi-people"></i></i>
              <span class="menu-title">Users</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="form-elements">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="pages/users/teacher.php">Teacher</a></li>
                <li class="nav-item"><a class="nav-link" href="pages/users/student.php">Student</a></li>
                <li class="nav-item"><a class="nav-link" href="pages/users/parent.php">Parent</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">
              <i class="menu-icon"><i class="bi bi-exclamation-triangle"></i></i>
              <span class="menu-title">Reports</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="charts">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="pages/reports/student_report.php">Student Reports</a>
                </li>
                <li class="nav-item"> <a class="nav-link" href="pages/reports/teacher_report.php">Teacher Reports</a>
                </li>
                <li class="nav-item"> <a class="nav-link" href="pages/reports/parent_report.php">Parent Reports</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" href="pages/feedback/feedback.php">
              <i class="menu-icon"><i class="bi bi-chat-right-quote"></i></i>
              <span class="menu-title">Feedbacks</span>
            </a>
          </li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h2 class="font-weight-bold">
                    Welcome Admin
                  </h2>
                  <p class="text-body-secondary">
                    Talisay Senior High School LMS
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 grid-margin transparent">
              <div class="card card-tale text-center">
                <div class="card-body">
                  <h5>STEM Department</h5>
                  <p class="mb-3">Users</p>
                  <?php
                  $dash_category_query = "SELECT * FROM user_account WHERE department='stem'";
                  $dash_category_query_run = mysqli_query($conn, $dash_category_query);

                  if ($category_total = mysqli_num_rows($dash_category_query_run)) {
                    echo '<h3> ' . $category_total . ' </h3>';
                  } else {
                    echo '<h3>0</h3>';
                  }
                  ?>
                  <img src="images/strand/stem.png" style="width: 20vh; height: 20vh;">
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin transparent">
              <div class="card card-dark-blue text-center">
                <div class="card-body">
                  <h5>HUMSS Department</h5>
                  <p class="mb-3">Users</p>
                  <?php
                  $dash_category_query = "SELECT * FROM user_account WHERE department='humss'";
                  $dash_category_query_run = mysqli_query($conn, $dash_category_query);

                  if ($category_total = mysqli_num_rows($dash_category_query_run)) {
                    echo '<h3> ' . $category_total . ' </h3>';
                  } else {
                    echo '<h3>0</h3>';
                  }
                  ?>
                  <img src="images/strand/humss.png" style="width: 20vh; height: 20vh;">
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin transparent">
              <div class="card card-light-blue text-center">
                <div class="card-body">
                  <h5>ABM Department</h5>
                  <p class="mb-3">Users</p>
                  <?php
                  $dash_category_query = "SELECT * FROM user_account WHERE department='abm'";
                  $dash_category_query_run = mysqli_query($conn, $dash_category_query);

                  if ($category_total = mysqli_num_rows($dash_category_query_run)) {
                    echo '<h3> ' . $category_total . ' </h3>';
                  } else {
                    echo '<h3>0</h3>';
                  }
                  ?>
                  <img src="images/strand/abm.png" style="width: 20vh; height: 20vh;">
                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin transparent">
              <div class="card card-light-danger text-center">
                <div class="card-body">
                  <h5>TVL Department</h5>
                  <p class="mb-3">Users</p>
                  <?php
                  $dash_category_query = "SELECT * FROM user_account WHERE department='tvl'";
                  $dash_category_query_run = mysqli_query($conn, $dash_category_query);

                  if ($category_total = mysqli_num_rows($dash_category_query_run)) {
                    echo '<h3> ' . $category_total . ' </h3>';
                  } else {
                    echo '<h3>0</h3>';
                  }
                  ?>
                  <img src="images/strand/mechanic.png" style="width: 20vh; height: 20vh;">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <?php
                  $sql = "SELECT * FROM news";
                  $result = mysqli_query($conn, $sql);
                  $totalAnnouncements = mysqli_num_rows($result);
                  ?>
                  <p class="card-title mb-0">Announcement <span class="text-body-secondary">(
                      <?php echo $totalAnnouncements ?> )
                    </span></p>
                  <?php
                  while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <a href="pages/announcement/announcement.php">
                      <h3 style="margin-top: 2vh;">
                        <?php echo $row['title'] ?>
                      </h3>
                      <p class="text-body-secondary">for
                        <?php echo strtoupper($row['track']); ?>
                      </p>
                      <p></p>
                    </a>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-3">Reports</p>
                  <?php
                  $sqlStudentReport = "SELECT * FROM user_account WHERE usertype = 'student'";
                  $studentResult = mysqli_query($conn, $sqlStudentReport);
                  $totalStudentReport = mysqli_num_rows($studentResult);
                  $studentRow = mysqli_fetch_assoc($studentResult);

                  if ($studentRow) {
                    $usertype = $studentRow['usertype'];
                    ?>
                    <p class="card-title mb-1">
                      <?php echo ucfirst($usertype) ?> Reports <span class="text-body-secondary">
                        (
                        <?php echo $totalStudentReport ?> )
                      </span>
                    </p>
                    <a href="pages/reports/student_report.php">View
                      <?php echo ucfirst($usertype) ?> Report
                    </a>
                    <?php
                  }

                  $sqlTeacherReport = "SELECT * FROM user_account WHERE usertype = 'teacher'";
                  $teacherResult = mysqli_query($conn, $sqlTeacherReport);
                  $totalTeacherReport = mysqli_num_rows($teacherResult);
                  $teacherRow = mysqli_fetch_assoc($teacherResult);

                  if ($teacherRow) {
                    $usertype = $teacherRow["usertype"];
                    ?>
                    <p class="card-title mt-4 mb-1">
                      <?php echo ucfirst($usertype) ?> Reports <span class="text-body-secondary">
                        (
                        <?php echo $totalTeacherReport ?> )
                      </span>
                    </p>
                    <a href="pages/reports/teacher_report.php">View
                      <?php echo ucfirst($usertype) ?> Report
                    </a>
                    <?php
                  }

                  $sqlParentReport = "SELECT * FROM user_account WHERE usertype='parent'";
                  $parentResult = mysqli_query($conn, $sqlParentReport);
                  $totalParentReport = mysqli_num_rows($parentResult);
                  $parentRow = mysqli_fetch_assoc($parentResult);

                  if ($parentRow) {
                    $usertype = $parentRow["usertype"];
                    ?>
                    <p class="card-title mt-4 mb-1">
                      <?php echo ucfirst($usertype) ?> Reports <span class="text-body-secondary">
                        (
                        <?php echo $totalParentReport ?> )
                      </span>
                    </p>
                    <a href="pages/reports/parent_report.php">View
                      <?php echo ucfirst($usertype) ?> Report
                    </a>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <?php
                  $sql = "SELECT * FROM feedback";
                  $result = mysqli_query($conn, $sql);
                  $totalFeedbacks = mysqli_num_rows($result);
                  ?>
                  <p class="card-title mb-0">Feedbacks <span class="text-body-secondary">(
                      <?php echo $totalFeedbacks ?> )
                    </span></p>
                  <?php
                  while ($row = mysqli_fetch_assoc($result)) {
                    $fullname = $row['firstname'] . ' ' . $row['lastname'];
                    ?>
                    <a href="pages/feedback/feedback.php">
                      <h3 style="margin-top: 2vh;">
                        <?php echo $row['report_title'] ?>
                      </h3>
                      <p class="text-body-secondary">by
                        <?php echo $fullname; ?>
                      </p>
                      <p></p>
                    </a>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script src="vendors/js/vendor.bundle.base.js"></script>
        <script src="vendors/chart.js/Chart.min.js"></script>
        <script src="vendors/datatables.net/jquery.dataTables.js"></script>
        <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
        <script src="js/dataTables.select.min.js"></script>
        <script src="js/off-canvas.js"></script>
        <script src="js/hoverable-collapse.js"></script>
        <script src="js/template.js"></script>
        <script src="js/settings.js"></script>
        <script src="js/todolist.js"></script>
        <script src="js/dashboard.js"></script>
        <script src="js/Chart.roundedBarCharts.js"></script>
</body>

</html>