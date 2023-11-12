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
  <link rel="stylesheet" href="assets/css/todolist.css">
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
      <!-- partial -->
      <div class="main-panel">
        <div class="header-sticky">
          <div class="header-links">
            <a class="btn-success" href="class_course.php?class_id=<?php echo $class_id ?>"><i
                class="bi bi-arrow-bar-left" style="color: white;"></i></a>
            <a href="todolist_assigned.php?class_id=<?php echo $class_id ?>&user_id=<?php echo $user_id ?>"
              class="nav-link active" style="margin-left: 2vh;">Assigned</a>
            <a href="todolist_missing.php?class_id=<?php echo $class_id ?>&user_id=<?php echo $user_id ?>"
              class="people">Missing</a>
            <a href="todolist_done.php?class_id=<?php echo $class_id ?>&user_id=<?php echo $user_id ?>"
              class="people">Done</a>
          </div>
        </div>
        <div class="content-wrapper align-items-center justify-content-center" style="margin-top: 10vh;">
          <div class="row align-items-center justify-content-center">
            <div class="col-md-3">
              <div class="card">
                <div class="card-body">
                  <h2 style="margin-bottom: -1px;">To-do List</h2>
                  <span class="text-body-secondary ml-1">(Assigned)</span>
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

              $sql_assignment = "SELECT a.assignment_id, a.title, a.date
                   FROM classwork_assignment a
                   LEFT JOIN student_assignment_course_answer sa
                   ON a.assignment_id = sa.assignment_id AND sa.user_id = ?
                   WHERE a.teacher_id = ? AND a.class_name = ? 
                   AND a.assignment_status = 'assigned'
                   AND sa.assignment_id IS NULL";
              $stmt_assignment = $db->prepare($sql_assignment);
              $stmt_assignment->execute([$user_id, $teacher_id, $class_name]);
              $assignment_results = $stmt_assignment->fetchAll();

              $sql_question = "SELECT q.question_id, q.title, q.date 
                 FROM classwork_question q
                 LEFT JOIN student_question_course_answer sq
                 ON q.question_id = sq.question_id AND sq.user_id = ?
                 WHERE q.teacher_id = ? AND q.class_name = ? 
                 AND q.question_status = 'assigned'
                 AND sq.question_id IS NULL";
              $stmt_question = $db->prepare($sql_question);
              $stmt_question->execute([$user_id, $teacher_id, $class_name]);
              $question_results = $stmt_question->fetchall();

              $sql_quiz = "SELECT q.quiz_id, q.quizTitle AS title, q.date 
              FROM classwork_quiz q
              LEFT JOIN student_quiz_course_answer sq
              ON q.quiz_id = sq.quiz_id AND sq.user_id = ?
              WHERE q.teacher_id = ? AND q.class_name = ? 
              AND q.quizStatus = 'assigned'
              AND sq.quiz_id IS NULL";
              $stmt_quiz = $db->prepare($sql_quiz);
              $stmt_quiz->execute([$user_id, $teacher_id, $class_name]);
              $quiz_results = $stmt_quiz->fetchAll();

              $sql_exam = "SELECT e.exam_id, e.examTitle AS title, e.date 
              FROM classwork_exam e
              LEFT JOIN student_exam_course_answer se
              ON e.exam_id = se.exam_id AND se.user_id = ?
              WHERE e.teacher_id = ? AND e.class_name = ? 
              AND e.examStatus = 'assigned'
              AND se.exam_id IS NULL";
              $stmt_exam = $db->prepare($sql_exam);
              $stmt_exam->execute([$user_id, $teacher_id, $class_name]);
              $exam_results = $stmt_exam->fetchAll();


              $combined_results = array_merge($assignment_results, $question_results, $quiz_results, $exam_results);
              usort($combined_results, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
              });

              foreach ($combined_results as $row) {
                if (isset($row['assignment_id'])) {
                  $assignment_id = $row['assignment_id'];
                  $title = $row['title'];
                  $date = $row['date'];
                  $timestamp = strtotime($date);
                  $formatted_date = date("F d", $timestamp);
                  ?>
                  <div class="d-grid gap-2 col-10 mx-auto mb-4">
                    <a class="announce" type="button"
                      href="assignment_course.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>&user_id=<?php echo $user_id ?>"
                      style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      <div
                        style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                        <i class="bi bi-journal-text" style="color: white; line-height: 42px; font-size: 25px;"></i>
                      </div>
                      <p
                        style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?php echo $title ?>
                      </p>
                      <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                        <?php echo $class_name ?>
                      </div>
                      <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                        <span>Posted
                          <?php echo $formatted_date ?>
                        </span>
                      </div>
                    </a>
                  </div>
                  <?php
                } elseif (isset($row['question_id'])) {
                  $question_id = $row['question_id'];
                  $title = $row['title'];
                  $date = $row['date'];
                  $timestamp = strtotime($date);
                  $formatted_date = date("F d", $timestamp);
                  ?>
                  <div class="d-grid gap-2 col-10 mx-auto mb-4">
                    <a class="announce" type="button"
                      href="question_course.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $question_id ?>&user_id=<?php echo $user_id ?>"
                      style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      <div
                        style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                        <i class="bi bi-question-square" style="color: white; line-height: 42px; font-size: 25px;"></i>
                      </div>
                      <p
                        style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?php echo $title ?>
                      </p>
                      <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                        <?php echo $class_name ?>
                      </div>
                      <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                        <span>Posted
                          <?php echo $formatted_date ?>
                        </span>
                      </div>
                    </a>
                  </div>
                  <?php
                } elseif (isset($row['quiz_id'])) {
                  $quiz_id = $row['quiz_id'];
                  $quizTitle = $row['title'];
                  $date = $row['date'];
                  $timestamp = strtotime($date);
                  $formatted_date = date("F d", $timestamp);
                  ?>
                  <div class="d-grid gap-2 col-10 mx-auto mb-4">
                    <a class="announce" type="button"
                      href="quiz_course.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $quiz_id ?>&user_id=<?php echo $user_id ?>"
                      style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      <div
                        style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                        <i class="bi bi-card-list" style="color: white; line-height: 42px; font-size: 25px;"></i>
                      </div>
                      <p
                        style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?php echo $quizTitle ?>
                      </p>
                      <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                        <?php echo $class_name ?>
                      </div>
                      <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                        <span>Posted
                          <?php echo $formatted_date ?>
                        </span>
                      </div>
                    </a>
                  </div>
                  <?php
                } elseif (isset($row['exam_id'])) {
                  $exam_id = $row['exam_id'];
                  $examTitle = $row['title'];
                  $date = $row['date'];
                  $timestamp = strtotime($date);
                  $formatted_date = date("F d", $timestamp);
                  ?>
                  <div class="d-grid gap-2 col-10 mx-auto mb-4">
                    <a class="announce" type="button"
                      href="exam_course.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $exam_id ?>&user_id=<?php echo $user_id ?>"
                      style="text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      <div
                        style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                        <i class="bi bi-card-list" style="color: white; line-height: 42px; font-size: 25px;"></i>
                      </div>
                      <p
                        style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?php echo $examTitle ?>
                      </p>
                      <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                        <?php echo $class_name ?>
                      </div>
                      <div style="margin-left: 45px; margin-top: 10px; margin-bottom: -10px; font-size: 14px;">
                        <span>Posted
                          <?php echo $formatted_date ?>
                        </span>
                      </div>
                    </a>
                  </div>
                  <?php
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
</body>

</html>