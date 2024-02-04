<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}
if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
}

$user_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'];
$tc_id = $_GET['tc_id'];

$sql_get_teacher_id = "SELECT teacher_id FROM class_enrolled WHERE class_id = ?";
$stmt_get_teacher_id = $db->prepare($sql_get_teacher_id);
$stmt_get_teacher_id->execute([$class_id]);
$teacher_id = $stmt_get_teacher_id->fetchColumn();

if ($teacher_id) {
  $sql_get_class_name = "SELECT class_name FROM class_enrolled WHERE class_id=?";
  $stmt_get_class_name = $db->prepare($sql_get_class_name);
  $stmt_get_class_name->execute([$class_id]);
  $class_name = $stmt_get_class_name->fetchColumn();

  $sqlGetClassid = "SELECT tc_id FROM class_enrolled WHERE class_id = ?";
  $stmtGetClassid = $db->prepare($sqlGetClassid);
  $stmtGetClassid->execute([$class_id]);
  $tc_id = $stmtGetClassid->fetchColumn();
}

$sql = "SELECT profile FROM user_profile WHERE user_id = :user_id AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_COLUMN);
$stmt->closeCursor();
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="assets/css/class_course.css">
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
      <div class="main-panel">
        <div class="header" style="overflow-y: auto; white-space: nowrap;">
          <div class="header-links">
            <?php
            if (isset($_GET['class_id'])) {
              $class_id = $_GET['class_id'];
              ?>
              <a class="btn-success" href="course.php"><i class="bi bi-arrow-bar-left" style="color: white;"></i></a>
              <a href="class_course.php?class_id=<?php echo $class_id ?>&tc_id=<?php echo $tc_id ?>" class="people"
                style="margin-left: 2vh;">Stream</a>
              <a href="class_classwork.php?class_id=<?php echo $class_id ?>&tc_id=<?php echo $tc_id ?>"
                class="people">Classwork</a>
              <a href="class_people.php?class_id=<?php echo $class_id ?>&tc_id=<?php echo $tc_id ?>"
                class="people">People</a>
              <a href="class_grade.php?class_id=<?php echo $class_id ?>&tc_id=<?php echo $tc_id ?>"
                class="nav-link active">Grade</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper">
          <div id="print-content">
            <div class="row">
              <div class="col-12 grid-margin stretch-card mb-4">
                <div class="card">
                  <div class="card-body">
                    <?php
                    include("config.php");
                    $sqlGradePercentage = "SELECT written, performance, exam, basegrade FROM section WHERE class_id = ? AND teacher_id = ?";
                    $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                    $stmtGradePercentage->execute([$tc_id, $teacher_id]);
                    $result = $stmtGradePercentage->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                      $written = $result['written'];
                      $performance = $result['performance'];
                      $exam = $result['exam'];
                      $basegrade = $result['basegrade'];
                      ?>
                      <div class="row">
                        <div class="col-12 mb-3">
                          <h2>Grading System</h2>
                        </div>
                        <div class="col-12 mb-3">
                          <h4>Written Work = <span>
                              <?php echo $written ?>%
                            </span></h3>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12 mb-3">
                          <h4>Performance Task = <span>
                              <?php echo $performance ?>%
                            </span></h3>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12 mb-3">
                          <h4>Quarterly Assessment = <span>
                              <?php echo $exam ?>%
                            </span></h3>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <h4>Base Grade = <span>
                              <?php echo $basegrade ?>
                            </span></h4>
                        </div>
                      </div>
                      <?php
                    }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card-body">
                        <h1 class="card-title" style="font-size: 30px; margin-left: 10px; margin-bottom: -20px;">Grade
                          Report in
                          <?php echo $class_name ?>
                        </h1>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card-body">
                          <div class="table-responsive">
                            <table id="example" class="table table-bordered table-hover text-center">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <th scope="col" style="overflow: hidden;">Student Name</th>
                                <?php
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlQuestion = "SELECT title, date, point, 'Question' as type FROM classwork_question 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuestion = $db->prepare($sqlQuestion);
                                $stmtQuestion->execute([$tc_id, $teacher_id]);
                                $questionTitles = $stmtQuestion->fetchAll(PDO::FETCH_ASSOC);

                                $sqlAssignment = "SELECT title, date, point, 'Assignment' as type FROM classwork_assignment 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtAssignment = $db->prepare($sqlAssignment);
                                $stmtAssignment->execute([$tc_id, $teacher_id]);
                                $assignmentTitles = $stmtAssignment->fetchAll(PDO::FETCH_ASSOC);

                                $sqlQuiz = "SELECT quizTitle as title, date, totalPoint as point, 'Quiz' as type FROM classwork_quiz 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuiz = $db->prepare($sqlQuiz);
                                $stmtQuiz->execute([$tc_id, $teacher_id]);
                                $quizTitles = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

                                $sqlExam = "SELECT examTitle as title, date, totalPoint as point, 'Exam' as type FROM classwork_exam 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtExam = $db->prepare($sqlExam);
                                $stmtExam->execute([$tc_id, $teacher_id]);
                                $examTitles = $stmtExam->fetchAll(PDO::FETCH_ASSOC);

                                $allTitles = array_merge($questionTitles, $assignmentTitles, $quizTitles, $examTitles);

                                usort($allTitles, function ($a, $b) {
                                  return strtotime($a['date']) - strtotime($b['date']);
                                });

                                foreach ($allTitles as $title) {
                                  $formattedDate = date("F j", strtotime($title['date']));
                                  ?>
                                  <th scope="col" style="text-align: left; overflow: hidden;">
                                    <p style="color: white; margin-bottom: -3px;">
                                      <?php echo $formattedDate; ?>
                                    </p>
                                    <p
                                      style="border-bottom: 1px solid white; color: black; width: 100%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                      <?php echo $title['title']; ?>
                                    </p>
                                    <p style="color: white;">HPS -
                                      <?php echo $title['point']; ?>
                                    </p>
                                  </th>
                                  <?php
                                }
                                ?>
                                <th scope="col" style="text-align: center; overflow: hidden;">
                                  <p style="color: white; margin-bottom: -3px;">Grade</p>
                                </th>
                              </thead>
                              <tbody>
                                <?php
                                $class_id = $_GET['class_id'];

                                $sqlAllStudent = "SELECT student_id, student_firstname, student_lastname FROM class_enrolled WHERE student_id = ? AND teacher_id = ?";
                                $stmtAllStudent = $db->prepare($sqlAllStudent);
                                $stmtAllStudent->execute([$user_id, $teacher_id]);
                                $student = $stmtAllStudent->fetch(PDO::FETCH_ASSOC);

                                if ($student) {
                                  ?>
                                  <tr>
                                    <td style="overflow: hidden;">
                                      <?php echo $student['student_lastname'] ?>
                                    </td>
                                    <?php
                                    foreach ($allTitles as $title) {
                                      $student_id = $student['student_id'];
                                      $questionTitle = $title['title'];
                                      $assignmentTitle = $title['title'];
                                      $quizTitle = $title['title'];
                                      $examTitle = $title['title'];

                                      $sqlQuestionScore = "SELECT gradeType, score, questionPoint 
                                      FROM questiongrade WHERE student_id = ? AND questionTitle = ?";
                                      $stmtQuestionScore = $db->prepare($sqlQuestionScore);
                                      $stmtQuestionScore->execute([$student_id, $questionTitle]);
                                      $questionScore = $stmtQuestionScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlAssignmentScore = "SELECT gradeType, score, assignmentPoint 
                                      FROM assignmentgrade WHERE student_id = ? AND assignmentTitle = ?";
                                      $stmtAssignmentScore = $db->prepare($sqlAssignmentScore);
                                      $stmtAssignmentScore->execute([$student_id, $assignmentTitle]);
                                      $assignmentScore = $stmtAssignmentScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlQuizScore = "SELECT gradeType, score, quizPoint 
                                      FROM quizgrade WHERE student_id = ? AND quizTitle = ?";
                                      $stmtQuizScore = $db->prepare($sqlQuizScore);
                                      $stmtQuizScore->execute([$student_id, $quizTitle]);
                                      $quizScore = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlExamScore = "SELECT score, examPoint 
                                      FROM examgrade WHERE student_id = ? AND examTitle = ?";
                                      $stmtExamScore = $db->prepare($sqlExamScore);
                                      $stmtExamScore->execute([$student_id, $examTitle]);
                                      $examScore = $stmtExamScore->fetch(PDO::FETCH_ASSOC);
                                      ?>
                                      <td>
                                        <?php
                                        echo isset($questionScore['score']) ? $questionScore['score'] . "<br>" : "";
                                        echo isset($assignmentScore['score']) ? $assignmentScore['score'] . "<br>" : "";
                                        echo isset($quizScore['score']) ? $quizScore['score'] . "<br>" : "";
                                        echo isset($examScore['score']) ? $examScore['score'] : "";
                                        ?>
                                      </td>
                                      <?php
                                    }
                                    $totalScore = 0;
                                    $totalPoints = 0;

                                    foreach ($allTitles as $title) {
                                      $questionTitle = $title['title'];
                                      $assignmentTitle = $title['title'];
                                      $quizTitle = $title['title'];
                                      $examTitle = $title['title'];

                                      $sqlTotalScores = "
                                      (SELECT score, gradeType, questionPoint AS point FROM questiongrade WHERE student_id = ? AND gradeType = 'written')
                                      UNION ALL
                                      (SELECT score, gradeType, assignmentPoint AS point FROM assignmentgrade WHERE student_id = ? AND gradeType = 'written')
                                      UNION ALL
                                      (SELECT score, gradeType, quizPoint AS point FROM quizgrade WHERE student_id = ? AND gradeType = 'written')
                                      UNION ALL
                                      (SELECT score, gradeType, questionPoint AS point FROM questiongrade WHERE student_id = ? AND gradeType = 'performance')
                                      UNION ALL
                                      (SELECT score, gradeType, assignmentPoint AS point FROM assignmentgrade WHERE student_id = ? AND gradeType = 'performance')
                                      UNION ALL
                                      (SELECT score, gradeType, quizPoint AS point FROM quizgrade WHERE student_id = ? AND gradeType = 'performance')
                                      UNION ALL
                                      (SELECT score, 'exam' as gradeType, examPoint AS point FROM examgrade WHERE student_id = ?)
                                      ";

                                      $stmtTotalScores = $db->prepare($sqlTotalScores);
                                      $stmtTotalScores->execute([$student_id, $student_id, $student_id, $student_id, $student_id, $student_id, $student_id]);

                                      $totalScores = $stmtTotalScores->fetchAll(PDO::FETCH_ASSOC);

                                      $totalWrittenScore = 0;
                                      $totalPerformanceScore = 0;
                                      $totalExamsScore = 0;

                                      $writtenScores = array_filter($totalScores, function ($score) {
                                        return $score['gradeType'] === 'written';
                                      });

                                      $performanceScores = array_filter($totalScores, function ($score) {
                                        return $score['gradeType'] === 'performance';
                                      });

                                      $examScores = array_filter($totalScores, function ($score) {
                                        return $score['gradeType'] === 'exam';
                                      });

                                      foreach ($writtenScores as $writtenscore) {
                                        $totalWrittenScore += $writtenscore['score'];
                                      }

                                      foreach ($performanceScores as $performancescore) {
                                        $totalPerformanceScore += $performancescore['score'];
                                      }

                                      foreach ($examScores as $examscore) {
                                        $totalExamsScore += $examscore['score'];
                                      }

                                      foreach ($totalScores as $score) {
                                        $sqlGradePercentage = "SELECT written, performance, exam, basegrade FROM section
                                        WHERE class_id = ? AND teacher_id = ?";
                                        $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                                        $stmtGradePercentage->execute([$tc_id, $teacher_id]);
                                        $result = $stmtGradePercentage->fetch(PDO::FETCH_ASSOC);

                                        $written = $result['written'];
                                        $writtenPercentage = $written / 100;

                                        $performance = $result['performance'];
                                        $performancePercentage = $performance / 100;

                                        $exam = $result['exam'];
                                        $examPercentage = $exam / 100;

                                        $basegrade = $result['basegrade'];
                                        $basegradeMinus = 100 - $basegrade;
                                        
                                        $sqlQuestionWrittenTotal = "SELECT point FROM classwork_question WHERE 
                                        class_id = ? AND teacher_id = ? AND type = 'written'";
                                        $stmtQuestionWrittenTotal = $db->prepare($sqlQuestionWrittenTotal);
                                        $stmtQuestionWrittenTotal->execute([$tc_id, $teacher_id]);
                                        $questionTotalPointsW = $stmtQuestionWrittenTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlAssignmentWrittenTotal = "SELECT point FROM classwork_assignment WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'written'";
                                        $stmtAssignmentWrittenTotal = $db->prepare($sqlAssignmentWrittenTotal);
                                        $stmtAssignmentWrittenTotal->execute([$tc_id, $teacher_id]);
                                        $assignmentTotalPointsW = $stmtAssignmentWrittenTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlQuizWrittenTotal = "SELECT totalPoint FROM classwork_quiz WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'written'";
                                        $stmtQuizWrittenTotal = $db->prepare($sqlQuizWrittenTotal);
                                        $stmtQuizWrittenTotal->execute([$tc_id, $teacher_id]);
                                        $quizTotalPointsW = $stmtQuizWrittenTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $writtenQuestionTotalPoints = array_sum($questionTotalPointsW);
                                        $writtenAssignmentTotalPoints = array_sum($assignmentTotalPointsW);
                                        $writtenQuizTotalPoints = array_sum($quizTotalPointsW);

                                        $totalWrittenPoints = $writtenQuestionTotalPoints + $writtenAssignmentTotalPoints + $writtenQuizTotalPoints;

                                        $sqlQuestionPerformanceTotal = "SELECT point FROM classwork_question WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $stmtQuestionPerformanceTotal = $db->prepare($sqlQuestionPerformanceTotal);
                                        $stmtQuestionPerformanceTotal->execute([$tc_id, $teacher_id]);
                                        $questionTotalPointsP = $stmtQuestionPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlAssignmentPerformanceTotal = "SELECT point FROM classwork_assignment WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $stmtAssignmentPerformanceTotal = $db->prepare($sqlAssignmentPerformanceTotal);
                                        $stmtAssignmentPerformanceTotal->execute([$tc_id, $teacher_id]);
                                        $assignmentTotalPointsP = $stmtAssignmentPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlQuizPerformanceTotal = "SELECT totalPoint FROM classwork_quiz WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $stmtQuizPerformanceTotal = $db->prepare($sqlQuizPerformanceTotal);
                                        $stmtQuizPerformanceTotal->execute([$tc_id, $teacher_id]);
                                        $quizTotalPointsP = $stmtQuizPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $performanceQuestionTotalPoints = array_sum($questionTotalPointsP);
                                        $performanceAssignmentTotalPoints = array_sum($assignmentTotalPointsP);
                                        $performanceQuizTotalPoints = array_sum($quizTotalPointsP);

                                        $totalPerformancePoints = $performanceQuestionTotalPoints + $performanceAssignmentTotalPoints + $performanceQuizTotalPoints;

                                        $sqlExamTotal = "SELECT totalPoint FROM classwork_exam WHERE class_id = ? AND teacher_id = ?";
                                        $stmtExamTotal = $db->prepare($sqlExamTotal);
                                        $stmtExamTotal->execute([$tc_id, $teacher_id]);
                                        $examTotalPoints = $stmtExamTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $totalExamPoints = array_sum($examTotalPoints);

                                        $writtenFirstResult = round(($totalWrittenScore * $basegradeMinus) / $totalWrittenPoints, 2);
                                        $performanceFirstResult = round(($totalPerformanceScore * $basegradeMinus) / $totalPerformancePoints, 2);
                                        $examFirstResult = round(($totalExamsScore * $basegradeMinus) / $totalExamPoints, 2);

                                        $writtenSecondResult = round($writtenFirstResult + $basegrade, 2);
                                        $performanceSecondResult = round($performanceFirstResult + $basegrade, 2);
                                        $examSecondResult = round($examFirstResult + $basegrade, 2);

                                        $writtenFinalResult = round($writtenSecondResult * $writtenPercentage, 2);
                                        $performanceFinalResult = round($performanceSecondResult * $performancePercentage, 2);
                                        $examFinalResult = round($examSecondResult * $examPercentage, 2);

                                        $finalGrade = $writtenFinalResult + $performanceFinalResult + $examFinalResult;
                                      }
                                    }
                                    if (!empty($totalScores)) {
                                      ?>
                                      <td style="color: <?php echo $finalGrade < 75 ? 'red' : 'green'; ?>">
                                        <?php echo $finalGrade ?>
                                      </td>
                                      <?php
                                    }
                                    ?>
                                  </tr>
                                  <?php
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
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="js/table2excel.js"></script>
    <script src="../../vendors/js/vendor.bundle.base.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
      $(document).ready(function () {
        $('#example').DataTable();
      });
    </script>
    <script>
      var form = document.getElementById('myForm');
      var validationAlert = document.getElementById('validationAlert');

      form.addEventListener('submit', function (event) {
        var classnameInput = form.querySelector('input[name="class_name"]');
        var sectionInput = form.querySelector('input[name="section"]');
        var subjectInput = form.querySelector('input[name="subject"]');
        var strandDropdown = form.querySelector('select[name="strand"]');

        if (classnameInput.value === '' || sectionInput.value === '' ||
          subjectInput.value === '' || strandDropdown.value === '') {
          event.preventDefault();
          validationAlert.style.display = 'block';

          // Scroll to the top
          setTimeout(function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            // Focus on the alert element
            validationAlert.focus();
          }, 100);
        }
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"
      integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa"
      crossorigin="anonymous"></script>
    <script src="../../js/off-canvas.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/settings.js"></script>
    <script src="../../js/todolist.js"></script>
    <!-- endinject -->
</body>

</html>