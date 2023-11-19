<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location:../../user_login.php");
  exit();
}

if (isset($_GET['user_id'])) {
  $user_id = $_GET['user_id'];
}

include("config.php");
$teacher_id = $_SESSION['user_id'];
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

      $sql_selectProfile = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
      $stmt_selectProfile = $db->prepare($sql_selectProfile);
      $profile_result = $stmt_selectProfile->execute([$childrenID]);
      $defaultProfile = "images/profile.png";

      if ($profile_result) {
        $profile_row = $stmt_selectProfile->fetch(PDO::FETCH_ASSOC);
        $otherProfile = !empty($profile_row['profile']) ? $profile_row['profile'] : $defaultProfile;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/report_grade.css">
  <link rel="stylesheet" href="assets/css/notification.css">
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
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
              aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
              <?php
              include("config.php");

              $sqlAnnounceNews = "SELECT news_id, title, name, type, date, end_date FROM NEWS";
              $stmtAnnounceNews = $db->prepare($sqlAnnounceNews);
              $stmtAnnounceNews->execute();
              $resultAnnounceNews = $stmtAnnounceNews->fetchAll(PDO::FETCH_ASSOC);

              foreach ($resultAnnounceNews as $news) {
                $news_id = $news['news_id'];
                $title = $news['title'];
                $name = $news['name'];
                $type = $news['type'];
                $date = date('M d', strtotime($news['date']));
                $end_date = $news['end_date'];
                $current_date = date('Y-m-d H:i:s');

                if ($current_date > $end_date) {
                  header('Location: index.php');
                  exit();
                }
                $link = ($type === 'news') ? 'news.php' : 'announcement.php';

                echo '<a href="view_' . $link . '?news_id='. $news_id .'" class="dropdown-item preview-item">';
                echo '<div class="preview-thumbnail">';
                echo '<div class="preview-icon bg-success">';
                echo '<i class="ti-info-alt mx-0"></i>';
                echo '</div>';
                echo '</div>';
                echo '<div class="preview-item-content">';
                echo '<h6 class="preview-subject font-weight-normal">' . $title . ' ' . $type . ' is posted</h6>';
                echo '<p class="font-weight-light small-text mb-0 text-muted">';
                echo 'In ' . $date . ' by ' . $name;
                echo '</p>';
                echo '</div>';
                echo '</a>';
              }
              ?>
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
                <li class="nav-item">
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
        <div class="header-links" style="overflow-x: auto; white-space: nowrap;">
          <?php
          $sql_childrenSubjects = "SELECT tc_id, subject, teacher_id FROM class_enrolled WHERE student_id = ?";
          $stmt_childrenSubjects = $db->prepare($sql_childrenSubjects);
          $stmt_childrenSubjects->execute([$childrenID]);
          $enrolledSubjects = $stmt_childrenSubjects->fetchAll(PDO::FETCH_ASSOC);

          if (!empty($enrolledSubjects)) {
            foreach ($enrolledSubjects as $enrolledSubject) {
              $class_id = $enrolledSubject['tc_id'];
              $subjectName = $enrolledSubject['subject'];
              $cssClass = (isset($_GET['subject']) && $_GET['subject'] === $subjectName) ? 'active' : '';
              $encodedSubject = urlencode($subjectName);
              $teacher_id = $enrolledSubject['teacher_id'];
              ?>
              <a href="subjectGrade_report.php?user_id=<?php echo $childrenID; ?>&class_id=<?php echo $class_id ?>&subject=<?php echo $encodedSubject; ?>&teacher_id=<?php echo $teacher_id ?>"
                class="nav-link <?php echo $cssClass; ?>">
                <?php echo $subjectName ?>
              </a>
              <?php
            }
          }
          ?>
        </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col d-flex align-items-center">
              <div class="circle-image mb-3 mr-3">
                <img src="../student/assets/image/<?php echo $otherProfile; ?>" alt="Circular Image"
                  onerror="this.src='images/profile.png'">
              </div>
              <h2>
                <?php echo $childrenFullName ?>
              </h2>
            </div>
          </div>
          <div class="row">
            <div class="col mt-4">
              <h3>Select subject above to view progress and performance in every subject.</h3>
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