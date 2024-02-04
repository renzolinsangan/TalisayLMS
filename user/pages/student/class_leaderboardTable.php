<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$tc_id = $_GET['tc_id'];

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

$sqlClassName = "SELECT class_name FROM class_enrolled WHERE class_id = ?";
$stmtClassName = $conn->prepare($sqlClassName);
$stmtClassName->bind_param("i", $class_id);
$stmtClassName->execute();
$stmtClassName->bind_result($class_name);
$stmtClassName->fetch();
$stmtClassName->close();

function calculatePoints($results, $pointPerItem)
{
  $totalPoints = 0;
  foreach ($results as $row) {
    $totalPoints += $row['score'];
  }
  return $totalPoints * $pointPerItem;
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
            $resultQuestionGradeNotif = getQuestionScoreNotification($db, $user_id);
            $resultAssignmentGradeNotif = getAssignmentScoreNotification($db, $user_id);

            $allNotifications = array_merge(
              $resultNewsNotif,
              $resultFriendNotif,
              $resultTeacherNotif,
              $resultMaterialNotif,
              $resultQuestionNotif,
              $resultAssignmentNotif,
              $resultQuestionGradeNotif,
              $resultAssignmentGradeNotif,
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
                        <?php
                        $link = ($notification['type'] === 'news') ? 'news.php' : 'announcement.php';

                        $end_date = $notification['end_date'];

                        // Check if the end_date has passed
                        $current_date = date('Y-m-d H:i:s');
                        $end_date_passed = ($current_date > $end_date);
                        ?>

                        <?php if ($end_date_passed): ?>
                          <h6 class="preview-subject font-weight-normal">
                            <?php echo $notification['title']; ?> (
                            <?php echo ucfirst($notification['type']); ?>)
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            by
                            <?php echo $notification['name']; ?> on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php else: ?>
                          <h6 class="preview-subject font-weight-normal"
                            onclick="window.location.href='view_<?php echo $link ?>?news_id=<?php echo $notification['news_id'] ?>'">
                            <?php echo $notification['title']; ?> (
                            <?php echo ucfirst($notification['type']); ?>)
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted"
                            onclick="window.location.href='view_<?php echo $link ?>?news_id=<?php echo $notification['news_id'] ?>'">
                            by
                            <?php echo $notification['name']; ?> on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php endif; ?>
                      <?php elseif (isset($notification['friend_id'])): ?>
                        <?php
                        $friendNameParts = explode(' ', $notification['name']);
                        $firstName = $friendNameParts[0];
                        ?>
                        <h6 class="preview-subject font-weight-normal" onclick="window.location.href='friends.php'">
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
                          <div class="preview-item-content" onclick="window.location.href='teacher.php'">
                            <h6 class="preview-subject font-weight-normal" onclick="window.location.href='teacher.php'">
                              You added
                              <?php echo $teacherName; ?> as your teacher.
                            </h6>
                            <p class="font-weight-light small-text mb-0 text-muted"
                              onclick="window.location.href='teacher.php'">
                              on
                              <?php echo date('F j', strtotime($notification['date'])); ?>
                            </p>
                          </div>
                        <?php else: ?>
                          <?php if ($notification['notification_type'] === 'material'): ?>
                            <div class="material-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a material in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'question'): ?>
                            <div class="question-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a question in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'assignment'): ?>
                            <div class="assignment-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted an assignment in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php endif; ?>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php endif; ?>
                      <?php elseif (isset($notification['score'])): ?>
                        <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                          <?php if ($notification['scoreNotification_type'] === 'questionGrade'): ?>
                            <?php echo $notification['teacherFirstName'] ?>
                            posted your score in
                            <?php echo $notification['questionTitle']; ?>
                            (question).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted" onclick="window.location.href='course.php'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'assignmentGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['assignmentTitle']; ?>
                          (assignment).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted" onclick="window.location.href='course.php'">
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
            <?php
            ?>
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
          <div class="row align-items-center justify-content-center">
            <div class="col-md-6 mb-3">
              <div class="card align-items-center justify-content-center" style="padding: 20px;">
                <h2>Leaderboard Table</h2>
                <a href="class_course.php?class_id=<?php echo $class_id ?>&tc_id=<?php echo $tc_id ?>" style="color: green;">
                  Click here to go back to class course.
                </a>
              </div>
            </div>
          </div>
          <div class="row align-items-center justify-content-center">
            <div class="col-md-6 align-items-center justify-content-center">
              <div class="card">
                <?php
                $sqlAllStudentsUserIds = "SELECT user_id FROM user_account WHERE usertype = 'student'";
                $stmtAllStudentsUserIds = $db->prepare($sqlAllStudentsUserIds);
                $stmtAllStudentsUserIds->execute();
                $allStudentsUserIds = $stmtAllStudentsUserIds->fetchAll(PDO::FETCH_COLUMN);

                $studentsWithPoints = [];

                foreach ($allStudentsUserIds as $user_id) {
                  $sqlAllStudent = "SELECT student_id, student_firstname, student_lastname FROM class_enrolled 
                  WHERE student_id = ? AND class_name = ?";
                  $stmtAllStudent = $db->prepare($sqlAllStudent);
                  $stmtAllStudent->execute([$user_id, $class_name]);
                  $studentResult = $stmtAllStudent->fetch(PDO::FETCH_ASSOC);

                  if ($studentResult) {
                    $student_firstname = $studentResult['student_firstname'];
                    $student_lastname = $studentResult['student_lastname'];
                    $student_id = $studentResult['student_id'];

                    $questionPoint = 10;
                    $assignmentPoint = 20;
                    $quizPoint = 40;
                    $examPoint = 50;

                    $sqlQuestionScoreToPoints = "SELECT score FROM questiongrade WHERE student_id = ?";
                    $stmtQuestionScoreToPoints = $db->prepare($sqlQuestionScoreToPoints);
                    $stmtQuestionScoreToPoints->execute([$student_id]);
                    $questionResults = $stmtQuestionScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                    $sqlAssignmentScoreToPoints = "SELECT score FROM assignmentgrade WHERE student_id = ?";
                    $stmtAssignmentScoreToPoints = $db->prepare($sqlAssignmentScoreToPoints);
                    $stmtAssignmentScoreToPoints->execute([$student_id]);
                    $assignmentResults = $stmtAssignmentScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                    $sqlQuizScoreToPoints = "SELECT score FROM quizgrade WHERE student_id = ?";
                    $stmtQuizScoreToPoints = $db->prepare($sqlQuizScoreToPoints);
                    $stmtQuizScoreToPoints->execute([$student_id]);
                    $quizResults = $stmtQuizScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                    $sqlExamScoreToPoints = "SELECT score FROM examgrade WHERE student_id = ?";
                    $stmtExamScoreToPoints = $db->prepare($sqlExamScoreToPoints);
                    $stmtExamScoreToPoints->execute([$student_id]);
                    $examResults = $stmtExamScoreToPoints->fetchAll(PDO::FETCH_ASSOC);

                    $questionPoints = calculatePoints($questionResults, $questionPoint);
                    $assignmentPoints = calculatePoints($assignmentResults, $assignmentPoint);
                    $quizPoints = calculatePoints($quizResults, $quizPoint);
                    $examPoints = calculatePoints($examResults, $examPoint);

                    $overallPoints = $questionPoints + $assignmentPoints + $quizPoints + $examPoints;

                    $studentsWithPoints[] = array(
                      'student_id' => $student_id,
                      'student_firstname' => $student_firstname,
                      'student_lastname' => $student_lastname,
                      'overallPoints' => $overallPoints
                    );
                  }
                }

                usort($studentsWithPoints, function ($a, $b) {
                  return $b['overallPoints'] - $a['overallPoints'];
                });
                ?>

                <!DOCTYPE html>
                <html lang="en">

                <head>
                  <meta charset="UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Student Rankings</title>
                </head>
                <body>
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Ranking</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Points</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $ranking = 1;
                      $counter = 0;
                      while ($counter < count($studentsWithPoints)) {
                        $student = $studentsWithPoints[$counter];

                        echo "<tr>";
                        echo "<th scope='row'>" . $ranking . "</th>";
                        echo "<td>" . $student['student_firstname'] . "</td>";
                        echo "<td>" . $student['student_lastname'] . "</td>";
                        echo "<td>" . $student['overallPoints'] . "</td>";
                        echo "</tr>";

                        $ranking++;
                        $counter++;
                      }
                      ?>
                    </tbody>
                  </table>
                  
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