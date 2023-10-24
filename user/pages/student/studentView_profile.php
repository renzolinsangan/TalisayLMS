<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql_profile = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $conn->prepare($sql_profile);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile);
$stmt->fetch();
$stmt->close();

if (isset($_GET['user_id'])) {
  $getUser_id = $_GET['user_id'];
}

$otherUser_id = $getUser_id;

$sql = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $otherUser_id);
$stmt->execute();
$stmt->bind_result($otherProfile);
$stmt->fetch();
$stmt->close();

$sql_student_info = "SELECT address, firstname, middlename, lastname, grade_level, department, section, usertype FROM user_account WHERE user_id=?";
$stmt = $conn->prepare($sql_student_info);

if ($stmt) {
  $stmt->bind_param("i", $otherUser_id);
  $stmt->execute();
  $stmt->bind_result($address, $firstname, $middlename, $lastname, $grade_level, $department, $section, $usertype);
  $stmt->fetch();
  $stmt->close();
}

if (isset($_POST['add_friend'])) {
  $user_id = $_SESSION['user_id'];
  $friend_id = $_GET['user_id'];
  $firstLetterOfMiddlename = ucfirst(substr($middlename, 0, 1));
  $name = $firstname . ' ' . $firstLetterOfMiddlename . '. ' . $lastname;

  $sql_addFriend = "INSERT INTO friend (user_id, friend_id, name) VALUES (?, ?, ?)";
  $stmt_addFriend = $conn->prepare($sql_addFriend);
  $result = $stmt_addFriend->execute([$user_id, $friend_id, $name]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS User</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/profile.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="assets/image/trace.svg" />
</head>

<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
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
    <!-- partial -->
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
                <li class="nav-item"><a class="nav-link" href="teacher.php">My Teachers</a></li>
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
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row justify-content-between">
                    <div class="col-md-3">
                      <div class="circle-image"
                        style="margin-left: 20px; background-image: url('<?php echo empty($otherProfile) ? 'images/profile.png' : 'assets/image/' . $otherProfile; ?>');">
                      </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center mt-3">
                      <div class="col-md-12">
                        <h2>
                          <?php echo $firstname . " " . (!empty($middlename) ? strtoupper(substr($middlename, 0, 1)) . "." : "") . " " . $lastname ?>
                        </h2>
                        <h3 class="mt-2" style="color: green;">
                          <?php echo strtoupper($department) ?>
                        </h3>
                        <p class="text-body-secondary">(
                          <?php echo $usertype ?>)
                        </p>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="d-flex flex-column justify-content-between h-100">
                        <div></div>
                        <div class="text-right">
                          <button class="btn edit" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                            style="color: green;">
                            + Add Friend
                          </button>
                        </div>
                        <form action="" method="post">
                          <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header" style="border: none;">
                                  <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                    Add Friend</h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <h3>Add
                                    <?php echo $firstname . ' ' . $lastname ?> as your friend.
                                  </h3>
                                  <p class="text-body-secondary">If you wish to cancel, press the x button.</p>
                                </div>
                                <div class="modal-footer" style="border: none;">
                                  <button type="submit" name="add_friend" class="btn btn-success">Add</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row justify-content-left align-items-center">
                      <div class="col-md-3">
                        <h3>
                          <?php echo ucfirst($usertype) ?> Information
                        </h3>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <hr class="mt-3" style="border-top: 2px solid black;">
                      </div>
                    </div>
                    <div class="row justify-content-left align-items-center">
                      <div class="col-md-5">
                        <h3 class="mb-3">Basic Information</h3>
                        <ul>
                          <li>
                            <p>Department:
                              <?php echo strtoupper($department) ?>
                            </p>
                          </li>
                          <li>
                            <p>Section:
                              <?php echo $section ?>
                            </p>
                          </li>
                          <li>
                            <p>Year Level:
                              <?php echo $grade_level ?>
                            </p>
                          </li>
                          <li>
                            <p>House Address:
                              <?php echo $address ?>
                            </p>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa"
        crossorigin="anonymous"></script>
      <script src="../../vendors/js/vendor.bundle.base.js"></script>
      <script src="../../js/off-canvas.js"></script>
      <script src="../../js/hoverable-collapse.js"></script>
      <script src="../../js/template.js"></script>
      <script src="../../js/settings.js"></script>
      <script src="../../js/todolist.js"></script>
</body>

</html>