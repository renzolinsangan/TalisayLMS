<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT profile FROM user_profile WHERE user_id = :user_id AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_COLUMN);
$stmt->closeCursor();

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
}

$sql_get_teacher_id = "SELECT teacher_id FROM class_enrolled WHERE class_id = ?";
$stmt_get_teacher_id = $db->prepare($sql_get_teacher_id);
$stmt_get_teacher_id->execute([$class_id]);
$teacher_id = $stmt_get_teacher_id->fetchColumn();

if ($teacher_id) {
  $sql_get_class_name = "SELECT class_name FROM class_enrolled WHERE class_id=?";
  $stmt_get_class_name = $db->prepare($sql_get_class_name);
  $stmt_get_class_name->execute([$class_id]);
  $class_name = $stmt_get_class_name->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS User</title>
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/toreview.css">
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
                <li class="nav-item"><a class="nav-link" href="friend.php">My Friends</a></li>
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
        <div class="header-sticky">
          <div class="header-links">
            <a class="btn-success" href="class_course.php?class_id=<?php echo $class_id ?>"><i
                class="bi bi-arrow-bar-left" style="color: white;"></i></a>
            <a href="toreview.php?class_id=<?php echo $class_id ?>" class="nav-link active"
              style="margin-left: 2vh;">To-review</a>
            <a href="#" class="people">Reviewed</a>
          </div>
        </div>
        <div class="content-wrapper align-items-center justify-content-center" style="margin-top: 10vh;">
          <div class="row align-items-center justify-content-center">
            <div class="col-md-3">
              <div class="card">
                <div class="card-body">
                  <h2 style="margin-bottom: -1px;">To-review</h2>
                  <span class="text-body-secondary ml-1">(Students Work)</span>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col">
              <?php
              $assignment_results = [];
              $question_results = [];
              $quiz_results = [];
              $exam_results = [];

              $sql_assignment = "SELECT assignment_id, title, class_name, due_date FROM classwork_assignment WHERE class_id = ?";
              $stmt_assignment = $db->prepare($sql_assignment);
              $stmt_assignment->execute([$class_id]);
              $assignment_results = $stmt_assignment->fetchAll();

              $sql_question = "SELECT question_id, title, class_name, due_date FROM classwork_question WHERE class_id = ?";
              $stmt_question = $db->prepare($sql_question);
              $stmt_question->execute([$class_id]);
              $question_results = $stmt_question->fetchall();

              $sql_quiz = "SELECT quiz_id, quizTitle, class_name, dueDate FROM classwork_quiz WHERE class_id = ?";
              $stmt_quiz = $db->prepare($sql_quiz);
              $stmt_quiz->execute([$class_id]);
              $quiz_results = $stmt_quiz->fetchAll();

              $sql_exam = "SELECT exam_id, examTitle, class_name, dueDate FROM classwork_exam WHERE class_id = ?";
              $stmt_exam = $db->prepare($sql_exam);
              $stmt_exam->execute([$class_id]);
              $exam_results = $stmt_exam->fetchAll();

              $combined_results = array_merge($assignment_results, $question_results, $quiz_results, $exam_results);
              usort($combined_results, function ($a, $b) {
                  $dueDateA = isset($a['dueDate']) ? $a['dueDate'] : $a['due_date'];
                  $dueDateB = isset($b['dueDate']) ? $b['dueDate'] : $b['due_date'];
              
                  return strtotime($dueDateA) - strtotime($dueDateB);
              });

              if (empty($combined_results)) {
                ?>
                <div class="d-grid gap-2 col-10 mx-auto">
                  <a class="announce" type="button" href="class_classwork.php?class_id=<?php echo $class_id ?>"
                  style="text-decoration: none;">
                    There are no assessments created to review, click this to go to classwork to create course assessment.
                  </a>
                </div>
                <?php
              } else {
                foreach ($combined_results as $row) {
                  if (isset($row['assignment_id'])) {
                    $assignment_id = $row['assignment_id'];
                    $title = $row['title'];
                    $class_name = $row['class_name'];
                    $due_date = $row['due_date'];
                    $timestamp = strtotime($due_date);
                    $formatted_date = date("F d", $timestamp);
                    ?>
                    <div class="d-grid gap-2 col-10 mx-auto mb-4">
                      <a class="announce" type="button"
                        href="assignment_review.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>"
                        style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div
                          style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                          <i class="bi bi-journal-text" style="color: white; line-height: 42px; font-size: 25px;"></i>
                        </div>
                        <p style="font-size: 17px; margin-top: -36px; margin-left: 7vh; 
                          white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                          Assignment: <?php echo $title ?>
                        </p>
                        <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                          <p class="text-body-secondary"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                            <?php echo $class_name ?> -
                            <span>
                              Due
                              <?php echo $formatted_date ?>
                            </span>
                          </p>
                        </div>
                      </a>
                    </div>
                    <?php
                  } elseif (isset($row['question_id'])) {
                    $question_id = $row['question_id'];
                    $title = $row['title'];
                    $class_name = $row['class_name'];
                    $due_date = $row['due_date'];
                    $timestamp = strtotime($due_date);
                    $formatted_date = date("F d", $timestamp);
                    ?>
                    <div class="d-grid gap-2 col-10 mx-auto mb-4">
                      <a class="announce" type="button"
                        href="question_review.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $question_id ?>"
                        style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div
                          style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                          <i class="bi bi-question-square" style="color: white; line-height: 42px; font-size: 25px;"></i>
                        </div>
                        <p style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; 
                          overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                          Question: <?php echo $title ?>
                        </p>
                        <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                          <p class="text-body-secondary"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                            <?php echo $class_name ?> -
                            <span>
                              Due
                              <?php echo $formatted_date ?>
                            </span>
                          </p>
                        </div>
                      </a>
                    </div>
                    <?php
                  } elseif(isset($row['quiz_id'])) {
                    $quiz_id = $row['quiz_id'];
                    $quizTitle = $row['quizTitle'];
                    $class_name = $row['class_name'];
                    $dueDate = $row['dueDate'];
                    $timestamp = strtotime($dueDate);
                    $formatted_date = date("F d", $timestamp);
                    ?>
                    <div class="d-grid gap-2 col-10 mx-auto mb-4">
                      <a class="announce" type="button"
                        href="quiz_review.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $quiz_id ?>"
                        style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div
                          style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                          <i class="bi bi-card-list" style="color: white; line-height: 42px; font-size: 25px;"></i>
                        </div>
                        <p style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; 
                          overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                          Quiz: <?php echo $quizTitle ?>
                        </p>
                        <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                          <p class="text-body-secondary"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                            <?php echo $class_name ?> -
                            <span>
                              Due
                              <?php echo $formatted_date ?>
                            </span>
                          </p>
                        </div>
                      </a>
                    </div>
                    <?php
                  } elseif(isset($row['exam_id'])) {
                    $exam_id = $row['exam_id'];
                    $examTitle = $row['examTitle'];
                    $class_name = $row['class_name'];
                    $dueDate = $row['dueDate'];
                    $timestamp = strtotime($dueDate);
                    $formatted_date = date("F d", $timestamp);
                    ?>
                    <div class="d-grid gap-2 col-10 mx-auto mb-4">
                      <a class="announce" type="button"
                        href="exam_review.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $exam_id ?>"
                        style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div
                          style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                          <i class="bi bi-card-list" style="color: white; line-height: 42px; font-size: 25px;"></i>
                        </div>
                        <p style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; 
                          overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                          Exam: <?php echo $examTitle ?>
                        </p>
                        <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                          <p class="text-body-secondary"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                            <?php echo $class_name ?> -
                            <span>
                              Due
                              <?php echo $formatted_date ?>
                            </span>
                          </p>
                        </div>
                      </a>
                    </div>
                    <?php
                  }
                }
              }
              ?>
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