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
                <li class="nav-item"> <a class="nav-link"
                    href="grade_report.php?user_id=<?php echo $teacher_id ?>">Report of Grades</a></li>
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
      <!-- partial -->
      <div class="main-panel">
        <div class="header-sticky" style="overflow-x: auto; white-space: nowrap;">
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
                    $sqlGradePercentage = "SELECT written, performance, exam FROM section WHERE class_id = ? AND teacher_id = ?";
                    $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                    $stmtGradePercentage->execute([$class_id, $user_id]);
                    $result = $stmtGradePercentage->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                      $written = $result['written'];
                      $performance = $result['performance'];
                      $exam = $result['exam'];
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
                        <div class="col-12">
                          <h4>Quarterly Assessment = <span>
                              <?php echo $exam ?>%
                            </span></h3>
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
                  <div class="card">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card-body">
                          <div class="table-responsive">
                            <table id="example" class="table table-bordered table-hover text-center"
                              style="width: 100%; table-layout: fixed; border-collapse: collapse;">
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

                                $sqlQuiz = "SELECT quizTitle as title, type, date, quizPoint as point, 'Quiz' as class_type FROM classwork_quiz 
                    WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuiz = $db->prepare($sqlQuiz);
                                $stmtQuiz->execute([$class_id, $teacher_id]);
                                $quizTitles = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

                                $sqlExam = "SELECT examTitle as title, date, examPoint as point, 'Exam' as class_type FROM classwork_exam 
                    WHERE class_id = ? AND teacher_id = ?";
                                $stmtExam = $db->prepare($sqlExam);
                                $stmtExam->execute([$class_id, $teacher_id]);
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
                                    <p style="color: white;">out of
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

                                    $totalScore = 0;
                                    $totalPoints = 0;

                                    foreach ($allTitles as $title) {
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

                                      $sqlGradePercentage = "SELECT written, performance, exam FROM section WHERE class_id = ? AND teacher_id = ?";
                                      $stmtGradePercentage = $db->prepare($sqlGradePercentage);
                                      $stmtGradePercentage->execute([$class_id, $user_id]);
                                      $result = $stmtGradePercentage->fetch(PDO::FETCH_ASSOC);
                                      
                                      $totalScore += isset($questionScore['score']) ? ($questionScore['score'] / $questionScore['questionPoint']) : 0;
                                      $totalScore += isset($assignmentScore['score']) ? ($assignmentScore['score'] / $assignmentScore['assignmentPoint']) : 0;
                                      $totalScore += isset($quizScore['score']) ? ($quizScore['score'] / $quizScore['quizPoint']) : 0;
                                      $totalScore += isset($examScore['score']) ? ($examScore['score'] / $examScore['examPoint']) : 0;

                                      $totalPoints += isset($questionScore['score']) ? 1 : 0;
                                      $totalPoints += isset($assignmentScore['score']) ? 1 : 0;
                                      $totalPoints += isset($quizScore['score']) ? 1 : 0;
                                      $totalPoints += isset($examScore['score']) ? 1 : 0;
                                    }

                                    $averageGrade = ($totalPoints > 0) ? ($totalScore / $totalPoints) : 0;
                                    $percentage = $averageGrade * 100;
                                    $color = ($percentage < 75) ? 'red' : 'green';

                                    ?>
                                    <td style="color: <?php echo $color; ?>">
                                      <?php echo number_format($percentage, 2) . '%'; ?>
                                    </td>
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