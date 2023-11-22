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
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/indexStyle.css">
  <link rel="stylesheet" href="assets/css/notification.css">
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
                          <?php elseif ($notification['notification_type'] === 'quiz'): ?>
                            <div class="quiz-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a quiz in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'exam'): ?>
                            <div class="exam-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal">
                                <?php echo $teacherName; ?> posted an exam in
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
                        <?php elseif ($notification['scoreNotification_type'] === 'quizGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['quizTitle']; ?>
                          (quiz).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted" onclick="window.location.href='course.php'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'examGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['examTitle']; ?>
                          (exam).
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
          <div class="row" style="margin-bottom: -20px;">
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
            <div class="col">
              <h3 class="mb-3">Enrolled Ongoing Courses</h3>
            </div>
          </div>
          <div class="row">
            <?php
            include("db_conn.php");

            $sqlCourses = "SELECT * FROM class_enrolled WHERE student_id = ? AND archive_status = ''";
            $stmtCourses = $conn->prepare($sqlCourses);
            $stmtCourses->bind_param("i", $user_id);
            $stmtCourses->execute();
            $coursesResult = $stmtCourses->get_result();

            // Check if there are enrolled courses
            if ($coursesResult->num_rows > 0) {
              while ($row = mysqli_fetch_assoc($coursesResult)) {
                // Loop through enrolled courses and display the cards
                $class_id = $row['class_id'];
                $class_name = $row['class_name'];
                $section = $row['section'];
                $teacherFirstName = $row['first_name'];
                $teacherLastName = $row['last_name'];
                $teacherFullName = $teacherFirstName . ' ' . $teacherLastName;
                ?>
                <div class="col-md-3 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <h5>
                        <?php echo $class_name ?>
                      </h5>
                      <p class="text-body-secondary">Section
                        <?php echo $section ?>
                      </p>
                      <p>by
                        <?php echo $teacherFullName ?>
                      </p>
                      <a href="class_course.php?class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>"
                        style="color: green;">
                        View Course Subject
                      </a>
                    </div>
                  </div>
                </div>
                <?php
              }
            } else {
              ?>
              <div class="col-md-4 mb-4">
                <div class="card">
                  <div class="card-body">
                    <h3>No Enrolled Courses</h3>
                    <p class="text-body-secondary">
                      You have not enrolled in any courses yet, click the link below to enroll.
                    </p>
                    <a href="course.php">Go to course section.</a>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
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
                  if ($totalNews == 0) {
                    ?>
                    <p class="text-body-secondary mt-3">There are no announcement posted or available at the moment.</p>
                    <?php
                  } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                      ?>
                      <a href="view_announcement.php?news_id=<?php echo $row['news_id'] ?>">
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
                  if ($totalNews == 0) {
                    ?>
                    <p class="text-body-secondary">There are no news posted or available at the moment.</p>
                    <?php
                  } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                      ?>
                      <a href="view_news.php?news_id=<?php echo $row['news_id'] ?>">
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