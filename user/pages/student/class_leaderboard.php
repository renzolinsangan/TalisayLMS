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

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/awards.css">
  <link rel="stylesheet" href="assets/css/notification.css">
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
            <?php
            include("config.php");
            include("notifications.php");

            $fullName = getFullName($db, $user_id);
            $studentFullName = ($fullName['firstname'] . ' ' . $fullName['lastname']);

            $resultNewsNotif = getNewsNotifications($db);
            $resultFriendNotif = getFriendNotifications($db, $user_id);
            $resultTeacherNotif = getTeacherNotifications($db, $user_id);
            $resultMaterialNotif = getMaterialNotifications($db, $studentFullName);
            $resultQuestionNotif = getQuestionNotification($db, $studentFullName);
            $resultAssignmentNotif = getAssignmentNotification($db, $studentFullName);
            $resultQuizNotif = getQuizNotification($db, $studentFullName);
            $resultExamNotif = getExamNotification($db, $studentFullName);
            $resultQuestionGradeNotif = getQuestionScoreNotification($db, $user_id);
            $resultAssignmentGradeNotif = getAssignmentScoreNotification($db, $user_id);
            $resultQuizGradeNotif = getQuizScoreNotification($db, $user_id);
            $resultExamGradeNotif = getExamScoreNotification($db, $user_id);

            $allNotifications = array_merge(
              $resultNewsNotif,
              $resultFriendNotif,
              $resultTeacherNotif,
              $resultMaterialNotif,
              $resultQuestionNotif,
              $resultAssignmentNotif,
              $resultQuizNotif,
              $resultExamNotif,
              $resultQuestionGradeNotif,
              $resultAssignmentGradeNotif,
              $resultQuizGradeNotif,
              $resultExamGradeNotif
            );
            usort($allNotifications, function ($a, $b) {
              return strtotime($b['date']) - strtotime($a['date']);
            });
            ?>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
              aria-labelledby="notificationDropdown">
              <div class="scrollable-notifications">
                <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                <?php foreach ($allNotifications as $notification): ?>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <?php if (isset($notification['title'])): ?>
                        <div class="preview-icon bg-success">
                          <i class="ti-info-alt mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['friend_id'])): ?>
                        <div class="preview-icon bg-warning">
                          <i class="ti-user mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['teacher_id'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-book mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['score'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="preview-item-content">
                      <?php if (isset($notification['title'])): ?>
                        <h6 class="preview-subject font-weight-normal">
                          <?php echo $notification['title']; ?> (
                          <?php echo ucfirst($notification['type']); ?>)
                        </h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          by
                          <?php echo $notification['name']; ?> on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['friend_id'])): ?>
                        <?php
                        $friendNameParts = explode(' ', $notification['name']);
                        $firstName = $friendNameParts[0];
                        ?>
                        <h6 class="preview-subject font-weight-normal">
                          You added
                          <?php echo $firstName; ?> as your friend.
                        </h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['teacher_id'])): ?>
                        <?php
                        $sqlTeacherName = "SELECT firstname FROM user_account WHERE user_id = :teacher_id";
                        $stmtTeacherName = $db->prepare($sqlTeacherName);
                        $stmtTeacherName->bindParam(':teacher_id', $notification['teacher_id']);
                        $stmtTeacherName->execute();
                        $teacherName = $stmtTeacherName->fetchColumn();
                        ?>
                        <?php if ($notification['notification_type'] === 'teacher'): ?>
                          <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">
                              You added
                              <?php echo $teacherName; ?> as your teacher.
                            </h6>
                          </div>
                        <?php else: ?>
                          <h6 class="preview-subject font-weight-normal">
                            <?php echo $teacherName; ?> posted
                            <?php if ($notification['notification_type'] === 'material'): ?>
                              a material in
                            <?php elseif ($notification['notification_type'] === 'question'): ?>
                              a question in
                            <?php elseif ($notification['notification_type'] === 'assignment'): ?>
                              an assignment in
                            <?php elseif ($notification['notification_type'] === 'quiz'): ?>
                              a quiz in
                            <?php elseif ($notification['notification_type'] === 'exam'): ?>
                              an exam in
                            <?php endif; ?>
                            <?php echo $notification['class_name']; ?>.
                          </h6>
                        <?php endif; ?>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['score'])): ?>
                        <h6 class="preview-subject font-weight-normal">
                          <?php if ($notification['scoreNotification_type'] === 'questionGrade'): ?>
                            <?php echo $notification['teacherFirstName'] ?>
                            posted your score in
                            <?php echo $notification['questionTitle']; ?>
                            (question).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'assignmentGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['assignmentTitle']; ?>
                          (assignment).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'quizGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['quizTitle']; ?>
                          (quiz).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'examGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['examTitle']; ?>
                          (exam).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
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
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-5 mb-3">
              <div class="card" style="padding: 20px;">
                <h2>Leaderboard Points</h2>
                <a href="class_course.php?class_id=<?php echo $class_id ?>" style="color: green;">
                  Click here to go back to class course.
                </a>
              </div>
            </div>
            <div class="col-12 grid-margin stretch-card">
              <div class="card position-relative">
                <div class="card-body">
                  <div class="row">
                    <div class="col d-flex align-items-center justify-content-center" style="height: 100%;">
                      <i class="bi bi-patch-question" style="font-size: 10vw; color: green; margin-right: 50px;"></i>
                    </div>
                    <div class="col-md-7">
                    <h2 style="color: green;">Question Leaderboard Points <i class="bi bi-award-fill"
                      style="font-size: 40px; color: green;"></i></h2>
                  <p class="text-body-secondary mb-3" style="font-size: 17px;">(1 score = 10 points)</p>
                  <?php
                  $sqlQuestionScoreToPoints = "SELECT questionTitle, score, questionPoint from questiongrade WHERE student_id = ?";
                  $stmtQuestionScoreToPoints = $db->prepare($sqlQuestionScoreToPoints);
                  $stmtQuestionScoreToPoints->execute([$user_id]);
                  $questionResults = $stmtQuestionScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                  if ($questionResults) {
                    foreach ($questionResults as $row) {
                      $questionTitle = $row['questionTitle'];
                      $questionScore = $row['score'];
                      $questionPoint = $row['questionPoint'];

                      $questionLeaderboardPoint = $questionScore * 10;
                      ?>
                      <h4 class="mb-3">
                        <?php echo $questionTitle ?> (
                        <?php echo $questionScore ?> /
                        <?php echo $questionPoint ?> ) =
                        <?php echo $questionLeaderboardPoint ?> Leaderboard Points
                      </h4>
                      <?php
                    }
                  } else {
                    ?>
                    <h3>There is no available question score to compute.</h3>
                    <?php
                  }
                  ?>
                </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="col-12 grid-margin stretch-card">
              <div class="card position-relative">
                <div class="card-body">
                  <div class="row">
                    <div class="col d-flex align-items-center justify-content-center" style="height: 100%;">
                      <i class="bi bi-book" style="font-size: 10vw; color: green; margin-right: 50px;"></i>
                    </div>
                    <div class="col-md-7">
                    <h2 style="color: green;">Assignment Leaderboard Points <i class="bi bi-award-fill"
                      style="font-size: 40px; color: green;"></i></h2>
                  <p class="text-body-secondary mb-3" style="font-size: 17px;">(1 score = 20 points)</p>
                  <?php
                  $sqlAssignmentScoreToPoints = "SELECT assignmentTitle, score, assignmentPoint from assignmentgrade WHERE student_id = ?";
                  $stmtAssignmentScoreToPoints = $db->prepare($sqlAssignmentScoreToPoints);
                  $stmtAssignmentScoreToPoints->execute([$user_id]);
                  $assignmentResults = $stmtAssignmentScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                  if ($assignmentResults) {
                    foreach ($assignmentResults as $row) {
                      $assignmentTitle = $row['assignmentTitle'];
                      $assignmentScore = $row['score'];
                      $assignmentPoint = $row['assignmentPoint'];

                      $assignmentLeaderboardPoint = $assignmentScore * 20;
                      ?>
                      <h4 class="mb-3">
                        <?php echo $assignmentTitle ?> (
                        <?php echo $assignmentScore ?> /
                        <?php echo $assignmentPoint ?> ) =
                        <?php echo $assignmentLeaderboardPoint ?> Leaderboard Points
                      </h4>
                      <?php
                    }
                  } else {
                    ?>
                    <h3>There is no available assignment score to compute.</h3>
                    <?php
                  }
                  ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 grid-margin stretch-card">
              <div class="card position-relative">
                <div class="card-body">
                  <div class="row">
                    <div class="col d-flex align-items-center justify-content-center" style="height: 100%;">
                      <i class="bi bi-card-checklist" style="font-size: 10vw; color: green; margin-right: 50px;"></i>
                    </div>
                    <div class="col-md-7">
                      <h2 style="color: green;">Quiz Leaderboard Points <i class="bi bi-award-fill"
                          style="font-size: 40px; color: green;"></i></h2>
                      <p class="text-body-secondary mb-3" style="font-size: 17px;">(1 score = 40 points)</p>
                      <?php
                      $sqlQuizScoreToPoints = "SELECT quizTitle, score, quizPoint from quizgrade WHERE student_id = ?";
                      $stmtQuizScoreToPoints = $db->prepare($sqlQuizScoreToPoints);
                      $stmtQuizScoreToPoints->execute([$user_id]);
                      $quizResults = $stmtQuizScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                      if ($quizResults) {
                        foreach ($quizResults as $row) {
                          $quizTitle = $row['quizTitle'];
                          $quizScore = $row['score'];
                          $quizPoint = $row['quizPoint'];

                          $quizLeaderboardPoint = $quizScore * 40;
                          ?>
                          <h4 class="mb-3">
                            <?php echo $quizTitle ?> (
                            <?php echo $quizScore ?> /
                            <?php echo $quizPoint ?> ) =
                            <?php echo $quizLeaderboardPoint ?> Leaderboard Points
                          </h4>
                          <?php
                        }
                      } else {
                        ?>
                        <h3>There is no available quiz score to compute.</h3>
                        <?php
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 grid-margin stretch-card">
              <div class="card position-relative">
                <div class="card-body">
                  <div class="row">
                    <div class="col d-flex align-items-center justify-content-center" style="height: 100%;">
                      <i class="bi bi-pencil-square" style="font-size: 10vw; color: green; margin-right: 50px;"></i>
                    </div>
                    <div class="col-md-7">
                      <h2 style="color: green;">Exam Leaderboard Points <i class="bi bi-award-fill"
                          style="font-size: 40px; color: green;"></i></h2>
                      <p class="text-body-secondary" style="font-size: 17px;">(1 score = 50 points)</p>
                      <?php
                      $sqlExamScoreToPoints = "SELECT examTitle, score, examPoint from examgrade WHERE student_id = ?";
                      $stmtExamScoreToPoints = $db->prepare($sqlExamScoreToPoints);
                      $stmtExamScoreToPoints->execute([$user_id]);
                      $examResults = $stmtExamScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                      if ($examResults) {
                        foreach ($examResults as $row) {
                          $examTitle = $row['examTitle'];
                          $examScore = $row['score'];
                          $examPoint = $row['examPoint'];

                          $examLeaderboardPoint = $examScore * 50;
                          ?>
                          <h4 class="mb-3">
                            <?php echo $examTitle ?> (
                            <?php echo $examScore ?> /
                            <?php echo $examPoint ?> ) =
                            <?php echo $examLeaderboardPoint ?> Leaderboard Points
                          </h4>
                          <?php
                        }
                      } else {
                        ?>
                        <h3>There is no available exam score to compute.</h3>
                        <?php
                      }
                      ?>
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
    <!-- endinject -->
</body>

</html>