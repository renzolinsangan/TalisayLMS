<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile);
$stmt->fetch();
$stmt->close();

$sql_department = "SELECT department FROM user_account WHERE user_id = ?";
$stmt_department = $conn->prepare($sql_department);
$stmt_department->bind_param("i", $user_id);
$stmt_department->execute();
$stmt_department->bind_result($department);
$stmt_department->fetch();
$stmt_department->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="images/trace.svg" />
</head>

<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="assets/image/trace.svg" class="mr-2"
            alt="logo" />Talisay LMS</a>
        <a class="navbar-brand brand-logo-mini" href="index.php"><img src="assets/image/trace.svg" alt="logo" /></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
              data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
              aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-success">
                    <i class="ti-info-alt mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Application Error</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    Just now
                  </p>
                </div>
              </a>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-warning">
                    <i class="ti-settings mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Settings</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    Private message
                  </p>
                </div>
              </a>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-info">
                    <i class="ti-user mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">New user registration</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    2 days ago
                  </p>
                </div>
              </a>
            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="assets/image/<?php echo $profile ?>" alt="profile" onerror="this.src='images/profile.png'" />
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a href="profile.php" class="dropdown-item">
                <i class="bi bi-person-circle text-success"></i>
                Profile
              </a>
              <a href="user_logout.php" class="dropdown-item">
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
            <a class="nav-link" href="course.php">
              <i class="menu-icon"><i class="bi bi-journals"></i></i>
              <span class="menu-title">Courses</span>
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
                <li class="nav-item"><a class="nav-link" href="friends.php">My Friends</a></li>
                <li class="nav-item"><a class="nav-link" href="teacher.php">My Teacher</a></li>
                <li class="nav-item"><a class="nav-link" href="parent.php">My Parent</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" href="awards.php">
              <i class="menu-icon"><i class="bi bi-award"></i></i>
              <span class="menu-title">Awards</span>
            </a>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" href="feedback.php">
              <i class="menu-icon"><i class="bi bi-chat-right-quote"></i></i>
              <span class="menu-title">Feedbacks</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h2 class="font-weight-bold">
                    Welcome Student
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
                  <img src="assets/image/stem.png" style="width: 20vh; height: 20vh;">
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
                  <img src="assets/image/humss.png" style="width: 20vh; height: 20vh;">
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
                  <img src="assets/image/abm.png" style="width: 20vh; height: 20vh;">
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
                  <img src="assets/image/mechanic.png" style="width: 20vh; height: 20vh;">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <?php
                  $currentDate = date("Y-m-d");
                  $userStrand = $department;

                  if ($userStrand == "all") {
                    $sql = "SELECT * FROM news WHERE type = 'announcement' AND end_date >= '$currentDate'";
                  } else {
                    $sql = "SELECT * FROM news WHERE type = 'announcement' AND (track = 'all' OR track = '$userStrand') AND end_date >= '$currentDate'";
                  }
                  $result = mysqli_query($conn, $sql);
                  $totalNews = mysqli_num_rows($result);
                  ?>
                  <p class="card-title mb-0">Announcement <span class="text-body-secondary">(
                      <?php echo $totalNews ?> )
                    </span></p>
                  <?php
                  while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <a href="view_announcement.php?news_id=<?php $row['news_id'] ?>">
                      <h3 style="margin-top: 2vh;">
                        <?php echo $row['title'] ?>
                      </h3>
                      <p class="text-body-secondary">
                        <?php echo $row['date'] ?>
                      </p>
                      <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                        <?php echo $row['detail'] ?>
                      </p>
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
                  $currentDate = date("Y-m-d");
                  $userStrand = $department;

                  if ($userStrand == "all") {
                    $sql = "SELECT * FROM news WHERE type = 'news' AND end_date >= '$currentDate'";
                  } else {
                    $sql = "SELECT * FROM news WHERE type = 'news' AND (track = 'all' OR track = '$userStrand') AND end_date >= '$currentDate'";
                  }
                  $result = mysqli_query($conn, $sql);
                  $totalNews = mysqli_num_rows($result);
                  ?>
                  <p class="card-title mb-0">News <span class="text-body-secondary">(
                      <?php echo $totalNews ?> )
                    </span></p>
                  <?php
                  while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <a href="view_news.php?news_id=<?php $row['news_id'] ?>">
                      <h3 style="margin-top: 2vh;">
                        <?php echo $row['title'] ?>
                      </h3>
                      <p class="text-body-secondary">
                        <?php echo $row['date'] ?>
                      </p>
                      <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                        <?php echo $row['detail'] ?>
                      </p>
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
                  <p class="card-title mb-0">Awards</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="js/dashboard.js"></script>
  <script src="js/Chart.roundedBarCharts.js"></script>
  <!-- End custom js for this page-->
</body>

</html>