<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
  $profile = $row['profile'];
}

$sql_children = "SELECT children FROM user_account WHERE user_id = ?";
$stmt_children = $db->prepare($sql_children);
$stmt_children->execute([$user_id]);
$row = $stmt_children->fetch(PDO::FETCH_ASSOC);

if ($row) {
  $children = $row['children'];
  $nameParts = explode(' ', $children);

  if (count($nameParts) >= 2) {
    $childFirstName = $nameParts[0] . ' ' . $nameParts[1];
    $childLastName = $nameParts[2];

    $sql_chosenChildren = "SELECT user_id, firstname, lastname FROM user_account WHERE firstname = ? AND lastname = ?";
    $stmt_chosenChildren = $db->prepare($sql_chosenChildren);
    $stmt_chosenChildren->execute([$childFirstName, $childLastName]);
    $childRow = $stmt_chosenChildren->fetch(PDO::FETCH_ASSOC);

    if ($childRow && isset($childRow['firstname']) && isset($childRow['lastname'])) {
      $childrenID = $childRow['user_id'];
      $childrenFirstName = $childRow['firstname'];
      $childrenLastName = $childRow['lastname'];
      $childrenFullName = $childrenFirstName . ' ' . $childrenLastName;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- Problem, wherein it only select one child-->

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS User</title>
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/add_friend.css">
  <link rel="shortcut icon" href="assets/image/trace.svg" />
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
            <a class="nav-link" href="children.php">
              <i class="menu-icon"><i class="bi bi-people"></i></i>
              <span class="menu-title">My Children</span>
            </a>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">
              <i class="menu-icon"><i class="bi bi-exclamation-triangle"></i></i>
              <span class="menu-title">Grade Report</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="charts">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item" >
                  <a class="nav-link" href="grade_report.php?user_id=<?php echo $childrenID ?>">
                    <?php echo $childrenLastName ?>
                  </a>
                </li>
              </ul>
            </div>
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
            <div class="col mb-3">
              <h2>Children</h2>
            </div>
          </div>
          <?php
          include("config.php");

          $sql_selectChild = "SELECT * FROM user_account WHERE user_id = ?";
          $stmt_selectChild = $db->prepare($sql_selectChild);
          $result = $stmt_selectChild->execute([$childrenID]);
          $childrenExist = false;

          if ($result) {
            $childrenExist = $stmt_selectChild->rowCount() > 0;
            if($childrenExist) {
              ?>
              <div class="row">
                <?php
                while ($row = $stmt_selectChild->fetch(PDO::FETCH_ASSOC)) {
  
                  $sql_selectProfile = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
                  $stmt_selectProfile = $db->prepare($sql_selectProfile);
                  $profile_result = $stmt_selectProfile->execute([$childrenID]);
  
                  $defaultProfile = "images/profile.png";
  
                  if ($profile_result) {
                    $profile_row = $stmt_selectProfile->fetch(PDO::FETCH_ASSOC);
                    $otherProfile = !empty($profile_row['profile']) ? $profile_row['profile'] : $defaultProfile;
                    ?>
                    <div class="col-md-4 mb-4">
                      <a href="grade_report.php?user_id=<?php echo $childrenID ?>" class="course">
                        <div class="card card-tale justify-content-center align-items-center"
                          style="background-image: url(../student/assets/image/user.png);">
                          <div class="circle-image mt-4 mb-3">
                            <img src="../student/assets/image/<?php echo $otherProfile; ?>" alt="Circular Image"
                              onerror="this.src='images/profile.png'">
                          </div>
                          <p class="text-body-secondary mb-4" style="font-size: 20px;">
                            <?php echo $childrenFullName ?>
                          </p>
                        </div>
                      </a>
                    </div>
                    <?php
                  }
                }
                ?>
              </div>
              <?php
            }
          }
          if (!$childrenExist) {
            ?>
            <div class="row">
              <div class="col-md-4">
                <div class="card">
                  <div class="card-body">
                    <h3>You have no children.</h3>
                    <p class="text-body-secondary">There is no registered account for your children, please proceed to feedback section to fix it.</p>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
          ?>
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