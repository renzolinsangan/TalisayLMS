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

$sql = "SELECT profile FROM user_profile WHERE user_id = :user_id AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_COLUMN);
$stmt->closeCursor();

$teacher_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'];

$sql = "SELECT class_name FROM section WHERE class_id = :class_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
  $class_name = $result['class_name'];
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="assets/css/class_course.css">
  <link rel="stylesheet" href="assets/css/notif.css">
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

            $resultNewsNotif = getNewsNotifications($db);
            $resultStudentNotif = getStudentNotifications($db, $user_id);
            $resultQuestionNotif = getQuestionNotifications($db, $user_id);
            $resultAssignmentNotif = getAssignmentNotifications($db, $user_id);
            $resultQuizNotif = getQuizNotifications($db, $user_id);
            $resultExamNotif = getExamNotifications($db, $user_id);
            $resultClassroomNotif = getClassroomNotifications($db, $user_id);

            $allNotifications = array_merge(
              $resultNewsNotif,
              $resultStudentNotif,
              $resultQuestionNotif,
              $resultAssignmentNotif,
              $resultQuizNotif,
              $resultExamNotif,
              $resultClassroomNotif
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
                      <?php if (isset($notification['title']) && isset($notification['type'])): ?>
                        <div class="preview-icon bg-success">
                          <i class="ti-info-alt mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['student_id'])): ?>
                        <div class="preview-icon bg-warning">
                          <i class="ti-user mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['class_name'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-blackboard mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['question_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['assignment_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['quiz_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['exam_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="preview-item-content">
                      <?php if (isset($notification['title']) && isset($notification['type'])): ?>
                        <h6 class="preview-subject font-weight-normal">
                          <?php echo $notification['title']; ?> (
                          <?php echo ucfirst($notification['type']); ?>)
                        </h6>
                      <?php elseif (isset($notification['student_id'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['student_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal">
                          You added
                          <?php echo $studentName; ?> as student.
                        </h6>
                      <?php elseif (isset($notification['class_name'])): ?>
                        <div class="preview-item-content">
                          <h6 class="preview-subject font-weight-normal">
                            <?php echo $notification['student_firstname']; ?> joined from
                            <?php echo $notification['class_name']; ?>
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </div>
                      <?php elseif (isset($notification['question_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal">
                          <?php echo $studentName; ?>
                          <?php echo $notification['question_course_status']; ?>
                          <?php echo $notification['title']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php elseif (isset($notification['assignment_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal">
                          <?php echo $studentName; ?>
                          <?php echo $notification['assignment_course_status']; ?>
                          <?php echo $notification['title']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php elseif (isset($notification['quiz_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal">
                          <?php echo $studentName; ?>
                          <?php echo $notification['quiz_course_status']; ?>
                          <?php echo $notification['quizTitle']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php elseif (isset($notification['exam_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal">
                          <?php echo $studentName; ?>
                          <?php echo $notification['exam_course_status']; ?>
                          <?php echo $notification['examTitle']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php endif; ?>
                      <?php if (isset($notification['name'])): ?>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          by
                          <?php echo $notification['name']; ?> on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
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
                <li class="nav-item"><a class="nav-link" href="student.php">My Students</a></li>
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
                <li class="nav-item"> <a class="nav-link"
                    href="student_report.php?user_id=<?php echo $teacher_id ?>">Student Reports</a></li>
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
        <div class="header-sticky" style="overflow-y: auto; white-space: nowrap;">
          <div class="header-links">
            <?php
            if (isset($_GET['class_id'])) {
              $class_id = $_GET['class_id'];
              ?>
              <a class="btn-success" href="course.php"><i class="bi bi-arrow-bar-left" style="color: white;"></i></a>
              <a href="class_course.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>"
                class="people" style="margin-left: 2vh;">Stream</a>
              <a href="class_classwork.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>"
                class="people">Classwork</a>
              <a href="class_people.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>"
                class="people">People</a>
              <a href="class_grade.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>"
                class="nav-link active">Grade</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper" style="margin-top: 10vh;">
          <div id="print-content">
            <div class="row">
              <div class="col-12 grid-margin stretch-card mb-4">
                <div class="card">
                  <div class="card-body">
                    <?php
                    include("config.php");
                    $sqlGradePercentage = "SELECT written, performance, exam, basegrade FROM section WHERE class_id = ? AND teacher_id = ?";
                    $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                    $stmtGradePercentage->execute([$class_id, $user_id]);
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
                          Reports in
                          <?php echo isset($_GET['class_name']) ? urldecode($_GET['class_name']) : 'Unknown Subject'; ?>
                        </h1>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-3 mb-2">
                    <div class="col-md-6" style="margin-left: 30px;">
                      <button id="exportButton" class="btn btn-success">Download data to Excel</button>
                    </div>
                  </div>
                  <div class="row mt-3 mb-2">
                    <div class="col-md-6" style="margin-left: 30px;">
                      <select class="form-select" id="tableSelector" style="padding: 5px; border-color: green;">
                        <option value="overall">Overall Grade</option>
                        <option value="written">Written Works</option>
                        <option value="performance">Performance Task</option>
                        <option value="assessment">Quarterly Assessment</option>
                      </select>
                    </div>
                  </div>
                  <div class="card">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card-body">
                          <div id="overallDiv" class="table-responsive">
                            <table id="overallTable" class="example table table-bordered table-hover text-center">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <th scope="col" style="text-align: center; overflow: hidden;">Student Name</th>
                                <?php
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlQuestion = "SELECT title, date, type, point, 'Question' as class_type FROM classwork_question 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuestion = $db->prepare($sqlQuestion);
                                $stmtQuestion->execute([$class_id, $teacher_id]);
                                $questionTitles = $stmtQuestion->fetchAll(PDO::FETCH_ASSOC);

                                $sqlAssignment = "SELECT title, date, type, point, 'Assignment' as class_type FROM classwork_assignment 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtAssignment = $db->prepare($sqlAssignment);
                                $stmtAssignment->execute([$class_id, $teacher_id]);
                                $assignmentTitles = $stmtAssignment->fetchAll(PDO::FETCH_ASSOC);

                                $sqlQuiz = "SELECT quizTitle as title, type, date, totalPoint as point, 'Quiz' as class_type FROM classwork_quiz 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuiz = $db->prepare($sqlQuiz);
                                $stmtQuiz->execute([$class_id, $teacher_id]);
                                $quizTitles = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

                                $sqlExam = "SELECT examTitle as title, COALESCE(NULL, 'exam') as type, date, totalPoint as point, 'Exam' as class_type FROM classwork_exam 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtExam = $db->prepare($sqlExam);
                                $stmtExam->execute([$class_id, $teacher_id]);
                                $examTitles = $stmtExam->fetchAll(PDO::FETCH_ASSOC);

                                $totalPointsQuestion = array_sum(array_column($questionTitles, 'point'));
                                $totalPointsAssignment = array_sum(array_column($assignmentTitles, 'point'));
                                $totalPointsQuiz = array_sum(array_column($quizTitles, 'point'));
                                $totalPointsExam = array_sum(array_column($examTitles, 'point'));

                                $totalPointsAll = $totalPointsQuestion + $totalPointsAssignment + $totalPointsQuiz + $totalPointsExam;

                                $allTitles = array_merge($questionTitles, $assignmentTitles, $quizTitles, $examTitles);

                                usort($allTitles, function ($a, $b) {
                                  return strtotime($a['date']) - strtotime($b['date']);
                                });

                                foreach ($allTitles as $title) {
                                  $formattedDate = date("F j", strtotime($title['date']));
                                  $materialType = $title['type'];
                                  if ($materialType === 'written') {
                                    $code = 'WW';
                                  } elseif ($materialType === 'performance') {
                                    $code = 'PT';
                                  } elseif ($materialType === 'exam') {
                                    $code = 'QA';
                                  }
                                  ?>
                                  <th scope="col" style="text-align: left; overflow: hidden;">
                                    <p style="margin-bottom: -3px;">
                                      <?php echo $title['title'] . ' - ' . $code ?>
                                    </p>
                                    <p
                                      style="border-bottom: 1px solid white; color: white; width: 100%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                      <?php echo $formattedDate; ?>
                                    </p>
                                    <p style="color: black;">HPS -
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
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlAllStudent = "SELECT student_id, student_firstname, student_lastname FROM class_enrolled WHERE tc_id = ? AND teacher_id = ?";
                                $stmtAllStudent = $db->prepare($sqlAllStudent);
                                $stmtAllStudent->execute([$class_id, $teacher_id]);
                                $students = $stmtAllStudent->fetchAll(PDO::FETCH_ASSOC);

                                usort($students, function ($a, $b) {
                                  return strcasecmp($a['student_lastname'], $b['student_lastname']);
                                });

                                foreach ($students as $student) {
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

                                      $sqlQuestionScore = "SELECT gradeType, score, questionPoint FROM questiongrade 
                                        WHERE student_id = ? AND questionTitle = ?";
                                      $stmtQuestionScore = $db->prepare($sqlQuestionScore);
                                      $stmtQuestionScore->execute([$student_id, $questionTitle]);
                                      $questionScore = $stmtQuestionScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlAssignmentScore = "SELECT gradeType, score, assignmentPoint FROM assignmentgrade 
                                      WHERE student_id = ? AND assignmentTitle = ?";
                                      $stmtAssignmentScore = $db->prepare($sqlAssignmentScore);
                                      $stmtAssignmentScore->execute([$student_id, $assignmentTitle]);
                                      $assignmentScore = $stmtAssignmentScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlQuizScore = "SELECT gradeType, score, quizPoint FROM quizgrade 
                                      WHERE student_id = ? AND quizTitle = ?";
                                      $stmtQuizScore = $db->prepare($sqlQuizScore);
                                      $stmtQuizScore->execute([$student_id, $quizTitle]);
                                      $quizScore = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlExamScore = "SELECT score, examPoint FROM examgrade 
                                      WHERE student_id = ? AND examTitle = ?";
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
                                        $stmtGradePercentage->execute([$class_id, $user_id]);
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
                                        $stmtQuestionWrittenTotal->execute([$class_id, $teacher_id]);
                                        $questionTotalPointsW = $stmtQuestionWrittenTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlAssignmentWrittenTotal = "SELECT point FROM classwork_assignment WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'written'";
                                        $stmtAssignmentWrittenTotal = $db->prepare($sqlAssignmentWrittenTotal);
                                        $stmtAssignmentWrittenTotal->execute([$class_id, $teacher_id]);
                                        $assignmentTotalPointsW = $stmtAssignmentWrittenTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlQuizWrittenTotal = "SELECT totalPoint FROM classwork_quiz WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'written'";
                                        $stmtQuizWrittenTotal = $db->prepare($sqlQuizWrittenTotal);
                                        $stmtQuizWrittenTotal->execute([$class_id, $teacher_id]);
                                        $quizTotalPointsW = $stmtQuizWrittenTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $writtenQuestionTotalPoints = array_sum($questionTotalPointsW);
                                        $writtenAssignmentTotalPoints = array_sum($assignmentTotalPointsW);
                                        $writtenQuizTotalPoints = array_sum($quizTotalPointsW);

                                        $totalWrittenPoints = $writtenQuestionTotalPoints + $writtenAssignmentTotalPoints + $writtenQuizTotalPoints;

                                        $sqlQuestionPerformanceTotal = "SELECT point FROM classwork_question WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $stmtQuestionPerformanceTotal = $db->prepare($sqlQuestionPerformanceTotal);
                                        $stmtQuestionPerformanceTotal->execute([$class_id, $teacher_id]);
                                        $questionTotalPointsP = $stmtQuestionPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlAssignmentPerformanceTotal = "SELECT point FROM classwork_assignment WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $stmtAssignmentPerformanceTotal = $db->prepare($sqlAssignmentPerformanceTotal);
                                        $stmtAssignmentPerformanceTotal->execute([$class_id, $teacher_id]);
                                        $assignmentTotalPointsP = $stmtAssignmentPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $sqlQuizPerformanceTotal = "SELECT totalPoint FROM classwork_quiz WHERE
                                        class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $stmtQuizPerformanceTotal = $db->prepare($sqlQuizPerformanceTotal);
                                        $stmtQuizPerformanceTotal->execute([$class_id, $teacher_id]);
                                        $quizTotalPointsP = $stmtQuizPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $performanceQuestionTotalPoints = array_sum($questionTotalPointsP);
                                        $performanceAssignmentTotalPoints = array_sum($assignmentTotalPointsP);
                                        $performanceQuizTotalPoints = array_sum($quizTotalPointsP);

                                        $totalPerformancePoints = $performanceQuestionTotalPoints + $performanceAssignmentTotalPoints + $performanceQuizTotalPoints;

                                        $sqlExamTotal = "SELECT totalPoint FROM classwork_exam WHERE class_id = ? AND teacher_id = ?";
                                        $stmtExamTotal = $db->prepare($sqlExamTotal);
                                        $stmtExamTotal->execute([$class_id, $teacher_id]);
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
                                    ?>
                                    <td style="color: <?php echo $finalGrade < 75 ? 'red' : 'green'; ?>">
                                      <?php echo $finalGrade ?>
                                    </td>
                                    <?php
                                    ?>
                                  </tr>
                                  <?php
                                }
                                ?>
                              </tbody>
                            </table>
                          </div>
                          <div id="writtenDiv" class="table-responsive" style="display: none;">
                            <table id="writtenTable" class="table table-bordered table-hover text-center">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <th scope="col" style="text-align: center; overflow: hidden;">Student Name</th>
                                <?php
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlQuestion = "SELECT title, date, type, point, 'Question' as class_type FROM classwork_question 
                                WHERE class_id = ? AND teacher_id = ? AND type = 'written'";
                                $stmtQuestion = $db->prepare($sqlQuestion);
                                $stmtQuestion->execute([$class_id, $teacher_id]);
                                $questionTitles = $stmtQuestion->fetchAll(PDO::FETCH_ASSOC);

                                $sqlAssignment = "SELECT title, date, type, point, 'Assignment' as class_type FROM classwork_assignment 
                                WHERE class_id = ? AND teacher_id = ? AND type='written'";
                                $stmtAssignment = $db->prepare($sqlAssignment);
                                $stmtAssignment->execute([$class_id, $teacher_id]);
                                $assignmentTitles = $stmtAssignment->fetchAll(PDO::FETCH_ASSOC);

                                $sqlQuiz = "SELECT quizTitle as title, type, date, totalPoint as point, 'Quiz' as class_type FROM classwork_quiz 
                                WHERE class_id = ? AND teacher_id = ? AND type = 'written'";
                                $stmtQuiz = $db->prepare($sqlQuiz);
                                $stmtQuiz->execute([$class_id, $teacher_id]);
                                $quizTitles = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

                                $totalPointsQuestion = array_sum(array_column($questionTitles, 'point'));
                                $totalPointsAssignment = array_sum(array_column($assignmentTitles, 'point'));
                                $totalPointsQuiz = array_sum(array_column($quizTitles, 'point'));

                                $totalPointsAll = $totalPointsQuestion + $totalPointsAssignment + $totalPointsQuiz;

                                $allTitles = array_merge($questionTitles, $assignmentTitles, $quizTitles);

                                usort($allTitles, function ($a, $b) {
                                  return strtotime($a['date']) - strtotime($b['date']);
                                });

                                foreach ($allTitles as $title) {
                                  $formattedDate = date("F j", strtotime($title['date']));
                                  $materialType = $title['type'];
                                  if ($materialType === 'performance') {
                                    $code = 'WW';
                                  }
                                  ?>
                                  <th scope="col" style="text-align: left; overflow: hidden;">
                                    <p style="margin-bottom: -3px;">
                                      <?php echo $title['title'] . ' - ' . $code ?>
                                    </p>
                                    <p
                                      style="border-bottom: 1px solid white; color: white; width: 100%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                      <?php echo $formattedDate; ?>
                                    </p>
                                    <p style="color: black;">HPS -
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
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlAllStudent = "SELECT student_id, student_firstname, student_lastname FROM class_enrolled WHERE tc_id = ? AND teacher_id = ?";
                                $stmtAllStudent = $db->prepare($sqlAllStudent);
                                $stmtAllStudent->execute([$class_id, $teacher_id]);
                                $students = $stmtAllStudent->fetchAll(PDO::FETCH_ASSOC);

                                usort($students, function ($a, $b) {
                                  return strcasecmp($a['student_lastname'], $b['student_lastname']);
                                });

                                foreach ($students as $student) {
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

                                      $sqlQuestionScore = "SELECT gradeType, score, questionPoint FROM questiongrade 
                                        WHERE student_id = ? AND questionTitle = ? AND gradeType='written'";
                                      $stmtQuestionScore = $db->prepare($sqlQuestionScore);
                                      $stmtQuestionScore->execute([$student_id, $questionTitle]);
                                      $questionScore = $stmtQuestionScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlAssignmentScore = "SELECT gradeType, score, assignmentPoint FROM assignmentgrade 
                                      WHERE student_id = ? AND assignmentTitle = ? AND gradeType='written'";
                                      $stmtAssignmentScore = $db->prepare($sqlAssignmentScore);
                                      $stmtAssignmentScore->execute([$student_id, $assignmentTitle]);
                                      $assignmentScore = $stmtAssignmentScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlQuizScore = "SELECT gradeType, score, quizPoint FROM quizgrade 
                                      WHERE student_id = ? AND quizTitle = ? AND gradeType='written'";
                                      $stmtQuizScore = $db->prepare($sqlQuizScore);
                                      $stmtQuizScore->execute([$student_id, $quizTitle]);
                                      $quizScore = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);
                                      ?>
                                      <td>
                                        <?php
                                        echo isset($questionScore['score']) ? $questionScore['score'] . "<br>" : "";
                                        echo isset($assignmentScore['score']) ? $assignmentScore['score'] . "<br>" : "";
                                        echo isset($quizScore['score']) ? $quizScore['score'] . "<br>" : "";
                                        ?>
                                      </td>
                                      <?php
                                    }
                                    foreach ($allTitles as $title) {
                                      $questionTitle = $title['title'];
                                      $assignmentTitle = $title['title'];
                                      $quizTitle = $title['title'];

                                      $sqlTotalScores = "
                                          (SELECT score, gradeType, questionPoint AS point FROM questiongrade WHERE student_id = ? AND gradeType = 'written')
                                          UNION ALL
                                          (SELECT score, gradeType, assignmentPoint AS point FROM assignmentgrade WHERE student_id = ? AND gradeType = 'written')
                                          UNION ALL
                                          (SELECT score, gradeType, quizPoint AS point FROM quizgrade WHERE student_id = ? AND gradeType = 'written')
                                      ";

                                      $stmtTotalScores = $db->prepare($sqlTotalScores);
                                      $stmtTotalScores->execute([$student_id, $student_id, $student_id]);
                                      $totalScores = $stmtTotalScores->fetchAll(PDO::FETCH_ASSOC);

                                      $totalWrittenScore = 0;

                                      $writtenScores = array_filter($totalScores, function ($score) {
                                        return $score['gradeType'] === 'written';
                                      });

                                      foreach ($writtenScores as $writtenscore) {
                                        $totalWrittenScore += $writtenscore['score'];
                                      }

                                      foreach ($totalScores as $score) {
                                        $sqlGradePercentage = "SELECT written, basegrade FROM section WHERE class_id = ? AND teacher_id = ?";
                                        $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                                        $stmtGradePercentage->execute([$class_id, $user_id]);
                                        $result = $stmtGradePercentage->fetch(PDO::FETCH_ASSOC);

                                        $written = $result['written'];
                                        $writtenPercentage = $written / 100;

                                        $basegrade = $result['basegrade'];
                                        $basegradeMinus = 100 - $basegrade;

                                        $sqlQuestionWrittenTotal = "SELECT point FROM classwork_question WHERE class_id = ? AND teacher_id = ? AND type = 'written'";
                                        $sqlAssignmentWrittenTotal = "SELECT point FROM classwork_assignment WHERE class_id = ? AND teacher_id = ? AND type = 'written'";
                                        $sqlQuizWrittenTotal = "SELECT totalPoint FROM classwork_quiz WHERE class_id = ? AND teacher_id = ? AND type = 'written'";

                                        $stmtQuestionWrittenTotal = $db->prepare($sqlQuestionWrittenTotal);
                                        $stmtAssignmentWrittenTotal = $db->prepare($sqlAssignmentWrittenTotal);
                                        $stmtQuizWrittenTotal = $db->prepare($sqlQuizWrittenTotal);

                                        $stmtQuestionWrittenTotal->execute([$class_id, $teacher_id]);
                                        $stmtAssignmentWrittenTotal->execute([$class_id, $teacher_id]);
                                        $stmtQuizWrittenTotal->execute([$class_id, $teacher_id]);

                                        $questionTotalPointsW = $stmtQuestionWrittenTotal->fetchAll(PDO::FETCH_COLUMN);
                                        $assignmentTotalPointsW = $stmtAssignmentWrittenTotal->fetchAll(PDO::FETCH_COLUMN);
                                        $quizTotalPointsW = $stmtQuizWrittenTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $writtenQuestionTotalPoints = array_sum($questionTotalPointsW);
                                        $writtenAssignmentTotalPoints = array_sum($assignmentTotalPointsW);
                                        $writtenQuizTotalPoints = array_sum($quizTotalPointsW);

                                        $totalWrittenPoints = $writtenQuestionTotalPoints + $writtenAssignmentTotalPoints + $writtenQuizTotalPoints;

                                        $writtenFirstResult = round(($totalWrittenScore * $basegradeMinus) / $totalWrittenPoints, 2);
                                        $writtenSecondResult = round($writtenFirstResult + $basegrade, 2);
                                        $writtenFinalResult = round($writtenSecondResult * $writtenPercentage, 2);
                                      }
                                    }

                                    ?>
                                    <td>
                                      <?php echo $writtenFinalResult ?>
                                    </td>
                                    <?php
                                    ?>
                                  </tr>
                                  <?php
                                }
                                ?>
                              </tbody>
                            </table>
                          </div>
                          <div id="performanceDiv" class="table-responsive" style="display: none;">
                            <table id="performanceTable" class="table table-bordered table-hover text-center">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <th scope="col" style="text-align: center; overflow: hidden;">Student Name</th>
                                <?php
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlQuestion = "SELECT title, date, type, point, 'Question' as class_type FROM classwork_question 
                                WHERE class_id = ? AND teacher_id = ? AND type = 'performance'";
                                $stmtQuestion = $db->prepare($sqlQuestion);
                                $stmtQuestion->execute([$class_id, $teacher_id]);
                                $questionTitles = $stmtQuestion->fetchAll(PDO::FETCH_ASSOC);

                                $sqlAssignment = "SELECT title, date, type, point, 'Assignment' as class_type FROM classwork_assignment 
                                WHERE class_id = ? AND teacher_id = ? AND type='performance'";
                                $stmtAssignment = $db->prepare($sqlAssignment);
                                $stmtAssignment->execute([$class_id, $teacher_id]);
                                $assignmentTitles = $stmtAssignment->fetchAll(PDO::FETCH_ASSOC);

                                $sqlQuiz = "SELECT quizTitle as title, type, date, totalPoint as point, 'Quiz' as class_type FROM classwork_quiz 
                                WHERE class_id = ? AND teacher_id = ? AND type = 'performance'";
                                $stmtQuiz = $db->prepare($sqlQuiz);
                                $stmtQuiz->execute([$class_id, $teacher_id]);
                                $quizTitles = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

                                $totalPointsQuestion = array_sum(array_column($questionTitles, 'point'));
                                $totalPointsAssignment = array_sum(array_column($assignmentTitles, 'point'));
                                $totalPointsQuiz = array_sum(array_column($quizTitles, 'point'));

                                $totalPointsAll = $totalPointsQuestion + $totalPointsAssignment + $totalPointsQuiz;

                                $allTitles = array_merge($questionTitles, $assignmentTitles, $quizTitles);

                                usort($allTitles, function ($a, $b) {
                                  return strtotime($a['date']) - strtotime($b['date']);
                                });

                                foreach ($allTitles as $title) {
                                  $formattedDate = date("F j", strtotime($title['date']));
                                  $materialType = $title['type'];
                                  if ($materialType === 'performance') {
                                    $code = 'PT';
                                  }
                                  ?>
                                  <th scope="col" style="text-align: left; overflow: hidden;">
                                    <p style="margin-bottom: -3px;">
                                      <?php echo $title['title'] . ' - ' . $code ?>
                                    </p>
                                    <p
                                      style="border-bottom: 1px solid white; color: white; width: 100%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                      <?php echo $formattedDate; ?>
                                    </p>
                                    <p style="color: black;">HPS -
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
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlAllStudent = "SELECT student_id, student_firstname, student_lastname FROM class_enrolled WHERE tc_id = ? AND teacher_id = ?";
                                $stmtAllStudent = $db->prepare($sqlAllStudent);
                                $stmtAllStudent->execute([$class_id, $teacher_id]);
                                $students = $stmtAllStudent->fetchAll(PDO::FETCH_ASSOC);

                                usort($students, function ($a, $b) {
                                  return strcasecmp($a['student_lastname'], $b['student_lastname']);
                                });

                                foreach ($students as $student) {
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

                                      $sqlQuestionScore = "SELECT gradeType, score, questionPoint FROM questiongrade 
                                        WHERE student_id = ? AND questionTitle = ? AND gradeType='performance'";
                                      $stmtQuestionScore = $db->prepare($sqlQuestionScore);
                                      $stmtQuestionScore->execute([$student_id, $questionTitle]);
                                      $questionScore = $stmtQuestionScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlAssignmentScore = "SELECT gradeType, score, assignmentPoint FROM assignmentgrade 
                                      WHERE student_id = ? AND assignmentTitle = ? AND gradeType='performance'";
                                      $stmtAssignmentScore = $db->prepare($sqlAssignmentScore);
                                      $stmtAssignmentScore->execute([$student_id, $assignmentTitle]);
                                      $assignmentScore = $stmtAssignmentScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlQuizScore = "SELECT gradeType, score, quizPoint FROM quizgrade 
                                      WHERE student_id = ? AND quizTitle = ? AND gradeType='performance'";
                                      $stmtQuizScore = $db->prepare($sqlQuizScore);
                                      $stmtQuizScore->execute([$student_id, $quizTitle]);
                                      $quizScore = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);
                                      ?>
                                      <td>
                                        <?php
                                        echo isset($questionScore['score']) ? $questionScore['score'] . "<br>" : "";
                                        echo isset($assignmentScore['score']) ? $assignmentScore['score'] . "<br>" : "";
                                        echo isset($quizScore['score']) ? $quizScore['score'] . "<br>" : "";
                                        ?>
                                      </td>
                                      <?php
                                    }
                                    foreach ($allTitles as $title) {
                                      $questionTitle = $title['title'];
                                      $assignmentTitle = $title['title'];
                                      $quizTitle = $title['title'];

                                      $sqlTotalScores = "
                                          (SELECT score, gradeType, questionPoint AS point FROM questiongrade WHERE student_id = ? AND gradeType = 'performance')
                                          UNION ALL
                                          (SELECT score, gradeType, assignmentPoint AS point FROM assignmentgrade WHERE student_id = ? AND gradeType = 'performance')
                                          UNION ALL
                                          (SELECT score, gradeType, quizPoint AS point FROM quizgrade WHERE student_id = ? AND gradeType = 'performance')
                                      ";

                                      $stmtTotalScores = $db->prepare($sqlTotalScores);
                                      $stmtTotalScores->execute([$student_id, $student_id, $student_id]);
                                      $totalScores = $stmtTotalScores->fetchAll(PDO::FETCH_ASSOC);

                                      $totalPerformanceScore = 0;

                                      $performanceScores = array_filter($totalScores, function ($score) {
                                        return $score['gradeType'] === 'performance';
                                      });

                                      foreach ($performanceScores as $performancescore) {
                                        $totalPerformanceScore += $performancescore['score'];
                                      }

                                      foreach ($totalScores as $score) {
                                        $sqlGradePercentage = "SELECT performance, basegrade FROM section WHERE class_id = ? AND teacher_id = ?";
                                        $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                                        $stmtGradePercentage->execute([$class_id, $user_id]);
                                        $result = $stmtGradePercentage->fetch(PDO::FETCH_ASSOC);

                                        $performance = $result['performance'];
                                        $performancePercentage = $performance / 100;

                                        $basegrade = $result['basegrade'];
                                        $basegradeMinus = 100 - $basegrade;

                                        $sqlQuestionPerformanceTotal = "SELECT point FROM classwork_question WHERE class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $sqlAssignmentPerformanceTotal = "SELECT point FROM classwork_assignment WHERE class_id = ? AND teacher_id = ? AND type = 'performance'";
                                        $sqlQuizPerformanceTotal = "SELECT totalPoint FROM classwork_quiz WHERE class_id = ? AND teacher_id = ? AND type = 'performance'";

                                        $stmtQuestionPerformanceTotal = $db->prepare($sqlQuestionPerformanceTotal);
                                        $stmtAssignmentPerformanceTotal = $db->prepare($sqlAssignmentPerformanceTotal);
                                        $stmtQuizPerformanceTotal = $db->prepare($sqlQuizPerformanceTotal);

                                        $stmtQuestionPerformanceTotal->execute([$class_id, $teacher_id]);
                                        $stmtAssignmentPerformanceTotal->execute([$class_id, $teacher_id]);
                                        $stmtQuizPerformanceTotal->execute([$class_id, $teacher_id]);

                                        $questionTotalPointsP = $stmtQuestionPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);
                                        $assignmentTotalPointsP = $stmtAssignmentPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);
                                        $quizTotalPointsP = $stmtQuizPerformanceTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $performanceQuestionTotalPoints = array_sum($questionTotalPointsP);
                                        $performanceAssignmentTotalPoints = array_sum($assignmentTotalPointsP);
                                        $performanceQuizTotalPoints = array_sum($quizTotalPointsP);

                                        $totalPerformancePoints = $performanceQuestionTotalPoints + $performanceAssignmentTotalPoints + $performanceQuizTotalPoints;

                                        $performanceFirstResult = round(($totalPerformanceScore * $basegradeMinus) / $totalPerformancePoints, 2);
                                        $performanceSecondResult = round($performanceFirstResult + $basegrade, 2);
                                        $performanceFinalResult = round($performanceSecondResult * $performancePercentage, 2);
                                      }
                                    }
                                    ?>
                                    <td>
                                      <?php echo $performanceFinalResult ?>
                                    </td>
                                    <?php
                                    ?>
                                  </tr>
                                  <?php
                                }
                                ?>
                              </tbody>
                            </table>
                          </div>
                          <div id="assessmentDiv" class="table-responsive" style="display: none;">
                            <table id="assessmentTable" class="table table-bordered table-hover text-center">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <th scope="col" style="text-align: center; overflow: hidden;">Student Name</th>
                                <?php
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlExam = "SELECT examTitle as title, COALESCE(NULL, 'exam') as type, date, totalPoint as point, 'Exam' as class_type FROM classwork_exam 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtExam = $db->prepare($sqlExam);
                                $stmtExam->execute([$class_id, $teacher_id]);
                                $examTitles = $stmtExam->fetchAll(PDO::FETCH_ASSOC);

                                $totalPointsExam = array_sum(array_column($examTitles, 'point'));

                                usort($examTitles, function ($a, $b) {
                                  return strtotime($a['date']) - strtotime($b['date']);
                                });

                                foreach ($examTitles as $title) {
                                  $formattedDate = date("F j", strtotime($title['date']));
                                  $materialType = $title['type'];
                                  $code = 'QA';

                                  ?>
                                  <th scope="col" style="text-align: left; overflow: hidden;">
                                    <p style="margin-bottom: -3px;">
                                      <?php echo $title['title'] . ' - ' . $code ?>
                                    </p>
                                    <p
                                      style="border-bottom: 1px solid white; color: white; width: 100%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                      <?php echo $formattedDate; ?>
                                    </p>
                                    <p style="color: black;">HPS -
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
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlAllStudent = "SELECT student_id, student_firstname, student_lastname FROM class_enrolled WHERE tc_id = ? AND teacher_id = ?";
                                $stmtAllStudent = $db->prepare($sqlAllStudent);
                                $stmtAllStudent->execute([$class_id, $teacher_id]);
                                $students = $stmtAllStudent->fetchAll(PDO::FETCH_ASSOC);

                                usort($students, function ($a, $b) {
                                  return strcasecmp($a['student_lastname'], $b['student_lastname']);
                                });

                                foreach ($students as $student) {
                                  ?>
                                  <tr>
                                    <td style="overflow: hidden;">
                                      <?php echo $student['student_lastname'] ?>
                                    </td>
                                    <?php
                                    foreach ($examTitles as $title) { // Assuming $examTitles is the correct array
                                      $student_id = $student['student_id'];
                                      $examTitle = $title['title'];

                                      $sqlExamScore = "SELECT score, examPoint FROM examgrade WHERE student_id = ? AND examTitle = ?";
                                      $stmtExamScore = $db->prepare($sqlExamScore);
                                      $stmtExamScore->execute([$student_id, $examTitle]);
                                      $examScore = $stmtExamScore->fetch(PDO::FETCH_ASSOC);
                                      ?>
                                      <td>
                                        <?php
                                        echo isset($examScore['score']) ? $examScore['score'] : "";
                                        ?>
                                      </td>
                                      <?php
                                    }
                                    foreach ($allTitles as $title) {
                                      $examTitle = $title['title'];

                                      $sqlTotalScores = " (SELECT score, 'exam' as gradeType, examPoint AS point FROM examgrade WHERE student_id = ?)";

                                      $stmtTotalScores = $db->prepare($sqlTotalScores);
                                      $stmtTotalScores->execute([$student_id]);
                                      $totalScores = $stmtTotalScores->fetchAll(PDO::FETCH_ASSOC);

                                      $totalExamsScore = 0;

                                      $examScores = array_filter($totalScores, function ($score) {
                                        return $score['gradeType'] === 'exam';
                                      });

                                      foreach ($examScores as $examscore) {
                                        $totalExamsScore += $examscore['score'];
                                      }

                                      foreach ($totalScores as $score) {
                                        $sqlGradePercentage = "SELECT exam, basegrade FROM section
                                        WHERE class_id = ? AND teacher_id = ?";
                                        $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                                        $stmtGradePercentage->execute([$class_id, $user_id]);
                                        $result = $stmtGradePercentage->fetch(PDO::FETCH_ASSOC);

                                        $exam = $result['exam'];
                                        $examPercentage = $exam / 100;

                                        $basegrade = $result['basegrade'];
                                        $basegradeMinus = 100 - $basegrade;

                                        $sqlExamTotal = "SELECT totalPoint FROM classwork_exam WHERE class_id = ? AND teacher_id = ?";
                                        $stmtExamTotal = $db->prepare($sqlExamTotal);
                                        $stmtExamTotal->execute([$class_id, $teacher_id]);
                                        $examTotalPoints = $stmtExamTotal->fetchAll(PDO::FETCH_COLUMN);

                                        $totalExamPoints = array_sum($examTotalPoints);
                                        $examFirstResult = round(($totalExamsScore * $basegradeMinus) / $totalExamPoints, 2);
                                        $examSecondResult = round($examFirstResult + $basegrade, 2);
                                        $examFinalResult = round($examSecondResult * $examPercentage, 2);
                                      }
                                    }
                                    ?>
                                    <td>
                                      <?php echo $totalExamsScore ?>
                                    </td>
                                    <?php
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

    <script src="../../vendors/js/vendor.bundle.base.js"></script>
    <script type="text/javascript" src="js/table2excel.js"></script>
    <script>
      document.getElementById('exportButton').addEventListener('click', function () {
        var table2excel = new Table2Excel();

        var table = document.querySelector("table");

        console.log('$title:', <?php echo json_encode($title['title']); ?>);
        console.log('$point:', <?php echo json_encode($title['point']); ?>);

        table2excel.export([table]);
      });

    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
      $(document).ready(function () {
        $('#overallTable').DataTable();
      });

      $(document).ready(function () {
        $('#writtenTable').DataTable();
      });

      $(document).ready(function () {
        $('#performanceTable').DataTable();
      });

      $(document).ready(function () {
        $('#assessmentTable').DataTable();
      });
    </script>
    <script>
      document.getElementById('tableSelector').addEventListener('change', function () {
        var selectedOption = this.value;

        document.getElementById('overallDiv').style.display = 'none';
        document.getElementById('writtenDiv').style.display = 'none';
        document.getElementById('performanceDiv').style.display = 'none';
        document.getElementById('assessmentDiv').style.display = 'none';

        document.getElementById(selectedOption + 'Div').style.display = 'block';
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