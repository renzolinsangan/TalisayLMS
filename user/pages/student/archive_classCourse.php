<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'];

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

$sql = "SELECT profile FROM user_profile WHERE user_id = :user_id AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_COLUMN);
$stmt->closeCursor();

$sql = "SELECT theme FROM class_theme WHERE class_name=:class_name AND teacher_id=:teacher_id AND theme_status='recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':class_name', $class_name, PDO::PARAM_STR);
$stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
$stmt->execute();
$theme = $stmt->fetch(PDO::FETCH_COLUMN);
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
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/class_course.css">
  <link rel="stylesheet" href="assets/css/notification.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="assets/image/trace.svg" />
</head>

<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="images/trace.svg" class="mr-2"
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
            <a href="archive.php" class="nav-link">
              <i class="menu-icon"><i class="bi bi-archive"></i></i>
              <span class="menu-title">Archive Courses</span>
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
            <?php
            if (isset($_GET['class_id'])) {
              $class_id = $_GET['class_id'];
              ?>
              <a class="btn-success" href="archive.php"><i class="bi bi-arrow-bar-left" style="color: white;"></i></a>
              <a href="archive_classCourse.php?class_id=<?php echo $class_id ?>" class="nav-link active"
                style="margin-left: 2vh;">Stream</a>
              <a href="archive_classClasswork.php?class_id=<?php echo $class_id ?>" class="people">Classwork</a>
              <a href="archive_classPeople.php?class_id=<?php echo $class_id ?>" class="people">People</a>
              <a href="archive_classGrade.php?class_id=<?php echo $class_id ?>" class="people">Grade</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 grid-margin stretch-card" style="margin-top: 10vh;">
              <div class="card">
                <div class="row">
                  <div class="col">
                    <div class="card-body" style="height: 45vh; display: flex; flex-direction: column; justify-content: flex-end;
                          <?php
                          if ($theme !== "../teacher/assets/image/") {
                            echo "background-image: url(../teacher/assets/image/$theme);";
                            echo "color: white;";
                          } else {
                            echo "background-color: green;";
                          }
                          ?>
                          background-color: green; background-size: cover; background-position: cover;">
                      <?php
                      include("db_conn.php");

                      if (isset($_GET['class_id'])) {
                        $class_id = $_GET['class_id'];

                        // Retrieve class details based on class_id
                        $sql = "SELECT * FROM class_enrolled WHERE class_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $class_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $class_data = $result->fetch_assoc();

                        if ($class_data) {
                          $class_name = $class_data['class_name'];
                          $section = $class_data['section'];
                          $class_code = $class_data['class_code'];
                          $_SESSION['first_name'] = $class_data['first_name'];
                          $_SESSION['last_name'] = $class_data['last_name'];

                          echo "<h2>$class_name</h2>";
                          echo "<h3>$section</h3>";
                        }
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="content-wrapper" style="margin-top: -50px;">
              <div class="row">
                <div class="col-md-3 mb-3">
                  <div class="card"
                    style="background-color: white; border-radius: 5; padding: 20px; border: 1px solid rgba(128, 128, 128, 0.5);">
                    <h4>To-do List</h4>
                    <p class="text-body-secondary mt-3">Check the works assigned to you.</p>
                    <a href="archive_todolistAssigned.php?class_id=<?php echo $class_id ?>&user_id=<?php echo $user_id ?>"
                      class="create mt-2" style="margin-left: auto; color: green;">View to-do list</a>
                  </div>
                </div>
                <div class="col">
                  <?php
                  $material_results = [];
                  $question_results = [];
                  $assignment_results = [];
                  $quiz_results = [];
                  $exam_results = [];

                  $sql_material = "SELECT material_id, title, date FROM classwork_material WHERE teacher_id=? AND class_name=?";
                  $stmt_titles_material = $db->prepare($sql_material);
                  $stmt_titles_material->execute([$teacher_id, $class_name]);
                  $material_results = $stmt_titles_material->fetchAll();

                  $sql_question = "SELECT question_id, title, date FROM classwork_question WHERE teacher_id=? AND class_name=?";
                  $stmt_titles_question = $db->prepare($sql_question);
                  $stmt_titles_question->execute([$teacher_id, $class_name]);
                  $question_results = $stmt_titles_question->fetchAll();

                  $sql_assignment = "SELECT assignment_id, title, date FROM classwork_assignment WHERE teacher_id=? AND class_name=?";
                  $stmt_titles_assignment = $db->prepare($sql_assignment);
                  $stmt_titles_assignment->execute([$teacher_id, $class_name]);
                  $assignment_results = $stmt_titles_assignment->fetchAll();

                  $sql_quiz = "SELECT quiz_id, quizTitle, date FROM classwork_quiz WHERE teacher_id = ? and class_name = ?";
                  $stmt_titles_quiz = $db->prepare($sql_quiz);
                  $stmt_titles_quiz->execute([$teacher_id, $class_name]);
                  $quiz_results = $stmt_titles_quiz->fetchAll();

                  $sql_exam = "SELECT exam_id, examTitle, date FROM classwork_exam WHERE teacher_id = ? AND class_name = ?";
                  $stmt_titles_exam = $db->prepare($sql_exam);
                  $stmt_titles_exam->execute([$teacher_id, $class_name]);
                  $exam_results = $stmt_titles_exam->fetchAll();

                  $combined_results = array_merge($material_results, $question_results, $assignment_results, $quiz_results, $exam_results);
                  usort($combined_results, function ($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                  });

                  if (empty($combined_results)) {
                    ?>
                    <div class="d-grid gap-2 col-13 mx-auto mb-4">
                      <span class="announce" type="button" href="#" style="text-decoration: none;">
                        There are no materials provided in this archived course.
                      </span>
                    </div>
                    <?php
                  } else {
                    foreach ($combined_results as $row) {
                      if (isset($row['material_id'])) {
                        $material_id = $row['material_id'];
                        $title = $row['title'];
                        $words = explode(' ', $title);
                        $maxWords = 6;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $date = $row['date'];
                        $formatted_date = date("F j", strtotime($date));

                        if (count($words) > $maxWords) {
                          $truncatedTitle .= '...';
                        }

                        ?>
                        <div class="d-grid gap-2 col-13 mx-auto mb-4">
                          <a class="announce" type="button"
                            href="archive_materialCourse.php?class_id=<?php echo $class_id ?>&material_id=<?php echo $material_id ?>"
                            style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <div
                              style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                              <i class="bi bi-journal-text" style="color: white; line-height: 42px; font-size: 25px;"></i>
                            </div>
                            <p
                              style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                              <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                              posted a new material:
                              <?php echo $truncatedTitle ?>
                            </p>
                            <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                              <?php echo $formatted_date ?>
                            </div>
                          </a>
                        </div>
                        <?php
                      } elseif (isset($row['question_id'])) {
                        $question_id = $row['question_id'];
                        $title = $row['title'];
                        $words = explode(' ', $title);
                        $maxWords = 6;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $date = $row['date'];
                        $formatted_date = date("F j", strtotime($date));

                        if (count($words) > $maxWords) {
                          $truncatedTitle .= '...';
                        }

                        ?>
                        <div class="d-grid gap-2 col-13 mx-auto mb-4">
                          <a class="announce" type="button"
                            href="archive_questionCourse.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $question_id ?>&user_id=<?php echo $user_id ?>"
                            style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <div
                              style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                              <i class="bi bi-question-square" style="color: white; line-height: 42px; font-size: 25px;"></i>
                            </div>
                            <p
                              style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                              <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                              posted a new question:
                              <?php echo $truncatedTitle ?>
                            </p>
                            <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                              <?php echo $formatted_date ?>
                            </div>
                          </a>
                        </div>
                        <?php
                      } elseif (isset($row['assignment_id'])) {
                        $assignment_id = $row['assignment_id'];
                        $title = $row['title'];
                        $words = explode(' ', $title);
                        $maxWords = 6;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $date = $row['date'];
                        $formatted_date = date("F j", strtotime($date));

                        if (count($words) > $maxWords)
                          ; {
                          $truncatedTitle .= '...';
                        }

                        ?>
                        <div class="d-grid gap-2 col-13 mx-auto mb-4">
                          <a class="announce" type="button"
                            href="archive_assignmentCourse.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>&user_id=<?php echo $user_id ?>"
                            style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <div
                              style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                              <i class="bi bi-journal-text" style="color: white; line-height: 42px; font-size: 25px;"></i>
                            </div>
                            <p
                              style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                              <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                              posted a new assignment:
                              <?php echo $truncatedTitle ?>
                            </p>
                            <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                              <?php echo $formatted_date ?>
                            </div>
                          </a>
                        </div>
                        <?php
                      } elseif (isset($row['quiz_id'])) {
                        $quiz_id = $row['quiz_id'];
                        $quizTitle = $row['quizTitle'];
                        $words = explode(' ', $quizTitle);
                        $maxWords = 6;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $date = $row['date'];
                        $formatted_date = date("F j", strtotime($date));

                        if (count($words) > $maxWords)
                          ; {
                          $truncatedTitle .= '...';
                        }

                        ?>
                        <div class="d-grid gap-2 col-13 mx-auto mb-4">
                          <a class="announce" type="button"
                            href="archive_quizCourse.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $quiz_id ?>&user_id=<?php echo $user_id ?>"
                            style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <div
                              style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                              <i class="bi bi-card-list" style="color: white; line-height: 42px; font-size: 25px;"></i>
                            </div>
                            <p
                              style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                              <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                              posted a new quiz:
                              <?php echo $truncatedTitle ?>
                            </p>
                            <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                              <?php echo $formatted_date ?>
                            </div>
                          </a>
                        </div>
                        <?php
                      } elseif (isset($row['exam_id'])) {
                        $exam_id = $row['exam_id'];
                        $examTitle = $row['examTitle'];
                        $words = explode(' ', $examTitle);
                        $maxWords = 6;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $date = $row['date'];
                        $formatted_date = date("F j", strtotime($date));

                        if (count($words) > $maxWords)
                          ; {
                          $truncatedTitle .= '...';
                        }

                        ?>
                        <div class="d-grid gap-2 col-13 mx-auto mb-4">
                          <a class="announce" type="button"
                            href="archive_examCourse.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $exam_id ?>&user_id=<?php echo $user_id ?>"
                            style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <div
                              style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                              <i class="bi bi-card-list" style="color: white; line-height: 42px; font-size: 25px;"></i>
                            </div>
                            <p
                              style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                              <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                              posted a new exam:
                              <?php echo $truncatedTitle ?>
                            </p>
                            <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                              <?php echo $formatted_date ?>
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
      </div>
    </div>

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
    <script src="../../vendors/js/vendor.bundle.base.js"></script>
</body>

</html>