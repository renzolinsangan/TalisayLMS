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

$teacher_id = $_SESSION['user_id'];

$sql = "SELECT class_name FROM section WHERE class_id = :class_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
  $class_name = $result['class_name'];
}

if (isset($_POST['submit_topic'])) {
  $class_topic = $_POST['class_topic'];

  $sql_section = "SELECT section, subject, strand, teacher_id, class_name FROM section WHERE class_id=?";
  $stmt_section = $db->prepare($sql_section);
  $stmt_section->execute([$class_id]);
  $section_data = $stmt_section->fetch(PDO::FETCH_ASSOC);

  if ($section_data) {
    $section = $section_data['section'];
    $subject = $section_data['subject'];
    $strand = $section_data['strand'];
    $teacher_id = $section_data['teacher_id'];
    $class_name = $section_data['class_name'];

    $sql_insert_topic = "INSERT INTO topic (class_topic, section, subject, strand, teacher_id, class_id, class_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_topic = $db->prepare($sql_insert_topic);
    $result = $stmt_insert_topic->execute([$class_topic, $section, $subject, $strand, $teacher_id, $class_id, $class_name]);

    if ($result) {
      header("Location: class_classwork.php?class_id=$class_id");
    } else {
      echo "Error inserting topic into the database.";
    }
  } else {
    echo "Error: Section information not found for the given class_id.";
  }
}

$user_id = $_SESSION['user_id'];

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
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/classwork_class.css">
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
        <div class="header-sticky">
          <div class="header-links" style="overflow-x: auto; white-space: nowrap;">
            <?php
            if (isset($_GET['class_id'])) {
              $class_id = $_GET['class_id'];
              ?>
              <a class="btn-success" href="course.php"><i class="bi bi-arrow-bar-left" style="color: white;"></i></a>
              <a href="class_course.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>" class="people"
                style="margin-left: 2vh;">Stream</a>
              <a href="class_classwork.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>" class="nav-link active">Classwork</a>
              <a href="class_people.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>" class="people">People</a>
              <a href="class_grade.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>" 
              class="people">Grade</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="modal fade" id="myModal">
              <form action="" method="post" class="forms-sample" id="myForm">
                <div class="modal-dialog modal-dialog-centered" style="top: -6vh;">
                  <div class="modal-content">
                    <div class="modal-body">
                      <h4 class="mb-4">Add Class Topic</h4>
                      <div id="validationAlert" class="alert alert-danger" style="display: none;">Class Topic cannot be
                        empty.</div>
                      <div class="form-floating mb-2">
                        <input type="text" name="class_topic" id="class_topic" class="form-control" id="floatingInput"
                          placeholder="Class Name">
                        <label for="floatingName">Class Topic</label>
                      </div>
                    </div>
                    <div class="modal-footer" style="border: none;">
                      <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                      <button type="submit" name="submit_topic" class="btn btn-success">Add</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="row">
            <div class="col-12 grid-margin stretch-card" style="margin-top: 10vh;">
              <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="bi bi-plus" style="font-size: 15px; margin-right: 5px;"></i>Create
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item" href="classwork_quiz.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-clipboard" style="color: green; margin-right: 10px;"></i>
                      Quiz
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_assignment.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-journal-text" style="color: green; margin-right: 10px;"></i>
                      Assignment
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_question.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-question-square" style="color: green; margin-right: 10px;"></i>
                      Question
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_material.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-journal-text" style="color: green; margin-right: 10px;"></i>
                      Material
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_exam.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-clipboard" style="color: green; margin-right: 10px;"></i>
                      Exam
                    </a>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <a class="nav-link dropdown-item" data-toggle="modal" data-target="#myModal"
                      style="cursor: pointer;">
                      <i class="bi bi-card-list" style="color: green; margin-right: 10px; margin-left: 10px;"></i>
                      Topic
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="row">
            <?php
            $_SESSION['class_topic'] = "";

            $sql_material = "SELECT material_id, title FROM classwork_material WHERE class_topic=? AND class_id=?";
            $stmt_titles_material = $db->prepare($sql_material);
            $stmt_titles_material->execute([$_SESSION['class_topic'], $class_id]);

            $sql_assignment = "SELECT assignment_id, title FROM classwork_assignment WHERE class_topic=? AND class_id=?";
            $stmt_titles_assignment = $db->prepare($sql_assignment);
            $stmt_titles_assignment->execute([$_SESSION['class_topic'], $class_id]);

            $sql_question = "SELECT question_id, title FROM classwork_question WHERE class_topic=? AND class_id=?";
            $stmt_titles_question = $db->prepare($sql_question);
            $stmt_titles_question->execute([$_SESSION['class_topic'], $class_id]);

            foreach ($stmt_titles_material as $title_row) {
              $material_id = $title_row['material_id'];
              $title = $title_row['title'];
              $words = explode(' ', $title);
              $maxWords = 4;
              $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));

              if (count($words) > $maxWords) {
                $truncatedTitle .= '...';
              }
              ?>
              <div class="material mb-3" href="">
                <div class="col-12 grid-margin strech-card" style="height: 4vh;">
                  <div class="card mb-3" style="border-radius: 0px; height: 10vh; width=100%;">
                    <div class="material-content"
                      style="display: flex; justify-content: space-between; align-items: center;">
                      <div class="material-body">
                        <div style="display: flex; align-items: center;">
                          <div
                            style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px; margin-top: 12px;">
                            <i class="bi bi-journal-text" style="color: white; line-height: 41px; font-size: 26px;"></i>
                          </div>
                          <p style="margin-top: 23px; margin-left: 30px; font-size: 17px;">
                            <?php echo $truncatedTitle ?>
                          </p>
                        </div>
                      </div>
                      <div class="dropdown mt-3">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                          style="font-size: 20px; color: green; margin-right: 10px;">
                          <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item"
                              href="edit_material.php?updateid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                          </li>
                          <li>
                            <a class="dropdown-item"
                              href="delete_material.php?deleteid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
            foreach ($stmt_titles_assignment as $titlerow) {
              $assignment_id = $titlerow['assignment_id'];
              $title = $titlerow['title'];
              $words = explode(' ', $title);
              $maxWords = 4;
              $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));

              if (count($words) > $maxWords) {
                $truncatedTitle .= '...';
              }
              ?>
              <div class="assignment mb-3" href="">
                <div class="col-12 grid-margin strech-card" style="height: 4vh;">
                  <div class="card mb-3" style="border-radius: 0px; height: 10vh; width=100%;">
                    <div class="assignment-content"
                      style="display: flex; justify-content: space-between; align-items: center;">
                      <div class="assignment-body">
                        <div style="display: flex; align-items: center;">
                          <div
                            style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px; margin-top: 12px;">
                            <i class="bi bi-journal-text" style="color: white; line-height: 41px; font-size: 26px;"></i>
                          </div>
                          <p style="margin-top: 23px; margin-left: 30px; font-size: 17px;">
                            <?php echo $truncatedTitle ?>
                          </p>
                        </div>
                      </div>
                      <div class="dropdown mt-3">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                          style="font-size: 20px; color: green; margin-right: 10px;">
                          <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item"
                              href="edit_assignment.php?updateid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                          </li>
                          <li>
                            <a class="dropdown-item"
                              href="delete_assignment.php?deleteid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
            foreach ($stmt_titles_question as $rowquestion) {
              $question_id = $rowquestion['question_id'];
              $title = $rowquestion['title'];
              $words = explode(' ', $title);
              $maxWords = 4;
              $truncatedQuestion = implode(' ', array_slice($words, 0, $maxWords));

              if (count($words) > $maxWords) {
                $truncatedTitle .= '...';
              }
              ?>
              <div class="question mb-4" href="">
                <div class="col-12 grid-margin strech-card" style="height: 4vh;">
                  <div class="card mb-3" style="border-radius: 0px; height: 10vh; width=100%;">
                    <div class="question-content"
                      style="display: flex; justify-content: space-between; align-items: center;">
                      <div class="question-body">
                        <div style="display: flex; align-items: center;">
                          <div
                            style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px; margin-top: 12px;">
                            <i class="bi bi-question-square"
                              style="color: white; line-height: 41px; font-size: 26px;"></i>
                          </div>
                          <p style="margin-top: 23px; margin-left: 30px; font-size: 17px;">
                            <?php echo $truncatedTitle ?>
                          </p>
                        </div>
                      </div>
                      <div class="dropdown mt-3">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                          style="font-size: 20px; color: green; margin-right: 10px;">
                          <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item"
                              href="edit_question.php?updateid=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                          </li>
                          <li>
                            <a class="dropdown-item"
                              href="delete_question.php?deleteid=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
          <div class="row">
            <?php
            include("db_conn.php");
            $sql = "SELECT class_topic FROM topic WHERE teacher_id=? AND class_id=$class_id";
            $stmt = $db->prepare($sql);
            $stmt->execute([$teacher_id]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $_SESSION['class_topic'] = $row['class_topic'];
              ?>
              <div class="col-12 grid-margin stretch-card">
                <div class="card" style="border-radius: 0px;">
                  <div class="topic" href="#">
                    <div class="topic-header mt-3 d-flex align-items-center justify-content-between">
                      <h2 class="topic_title mb-3" style="margin-left: 5vh; color: green;">
                        <?php echo ucfirst($row['class_topic']) ?>
                      </h2>
                      <div class="dropdown mb-3">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                          style="font-size: 20px; color: green; margin-right: 10px;">
                          <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item" data-toggle="modal" data-target="#editMyModal"
                              style="cursor: pointer;">Delete</a>
                          </li>
                        </ul>
                        <div class="modal fade" id="editMyModal">
                          <form action="" method="post" class="forms-sample" id="myEditForm">
                            <div class="modal-dialog modal-dialog-centered" style="top: -6vh;">
                              <div class="modal-content">
                                <div class="modal-body">
                                  <h4 class="mb-4">Rename Class Topic</h4>
                                  <div id="editedValidationAlert" class="alert alert-danger" style="display: none;">Class
                                    Topic cannot be
                                    empty.</div>
                                  <div class="form-floating mb-2">
                                    <input type="text" name="rename_topic" id="rename_topic" class="form-control"
                                      id="floatingInput" placeholder="Class Name">
                                    <label for="floatingName">Class Topic</label>
                                  </div>
                                </div>
                                <div class="modal-footer" style="border: none;">
                                  <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                  <button type="submit" name="submit_topic" class="btn btn-success">Add</button>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <p class="bottom-border mb-4"></p>
                    <div class="topic-body">
                      <?php
                      $counter = 1;

                      $sql_material = "SELECT material_id, title, description, date, link, file, youtube 
                        FROM classwork_material WHERE class_topic=? AND class_id=?";
                      $stmt_titles_material = $db->prepare($sql_material);
                      $stmt_titles_material->execute([$_SESSION['class_topic'], $class_id]);

                      $sql_assignment = "SELECT assignment_id, title, instruction, date, due_date, link, file, youtube 
                        FROM classwork_assignment WHERE class_topic=? AND class_id=?";
                      $stmt_titles_assignment = $db->prepare($sql_assignment);
                      $stmt_titles_assignment->execute([$_SESSION['class_topic'], $class_id]);

                      $sql_question = "SELECT question_id, title, instruction, date, due_date, link, file, youtube 
                        FROM classwork_question WHERE class_topic=? AND class_id=?";
                      $stmt_titles_question = $db->prepare($sql_question);
                      $stmt_titles_question->execute([$_SESSION['class_topic'], $class_id]);

                      $sql_quiz = "SELECT quiz_id, quizTitle, quizInstruction, quizLink, date, dueDate 
                        FROM classwork_quiz WHERE classTopic=? AND class_id=?";
                      $stmt_titles_quiz = $db->prepare($sql_quiz);
                      $stmt_titles_quiz->execute([$_SESSION['class_topic'], $class_id]);

                      $sql_exam = "SELECT exam_id, examTitle, examInstruction, examLink, date, dueDate
                        FROM classwork_exam WHERE classTopic = ? AND class_id = ?";
                      $stmt_titles_exam = $db->prepare($sql_exam);
                      $stmt_titles_exam->execute([$_SESSION['class_topic'], $class_id]);

                      foreach ($stmt_titles_material as $title_row) {
                        $material_id = $title_row['material_id'];
                        $title = $title_row['title'];
                        $description = $title_row['description'];
                        $date = $title_row['date'];
                        $formattedDate = date("F j", strtotime($date));
                        $words = explode(' ', $title);
                        $maxWords = 4;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $collapseID = "collapseMaterial" . $counter;
                        $link = $title_row['link'];
                        $file = $title_row['file'];
                        $fileDirectory = "assets/uploads/";
                        $filePath = $fileDirectory . $file;
                        $youtube = $title_row['youtube'];

                        if (count($words) > $maxWords) {
                          $truncatedTitle .= '...';
                        }
                        ?>
                        <div class="topic-material">
                          <div class="col-12 grid-margin strech-card">
                            <div class="body-card"
                              style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                              <button class="d-flex justify-content-between align-items-center" data-toggle="collapse"
                                data-target="#<?php echo $collapseID ?>"
                                style="width: 100%; height: 100%; border: none; background-color: transparent;">
                                <div style="display: flex; align-items: center;">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-journal-text"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 12px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="ml-auto">
                                  <p class="text-body-secondary" style="margin-top: 12px;">Posted
                                    <?php echo $formattedDate ?>
                                  </p>
                                </div>
                              </button>
                              <div class="dropdown">
                                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                  style="font-size: 20px; color: green; margin-right: 10px;">
                                  <i class="bi bi-three-dots-vertical"></i>
                                </a>

                                <ul class="dropdown-menu">
                                  <li>
                                    <a class="dropdown-item"
                                      href="edit_material.php?updateid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_material.php?deleteid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div id="<?php echo $collapseID ?>" class="collapse" aria-labelledby="accordionHeading">
                              <div class="card-body" style="border: 1px solid #ccc;">
                                <div class="row mb-2">
                                  <div class="col-md-8">
                                    <p class="text-body-secondary">Posted
                                      <?php echo $formattedDate ?>
                                    </p>
                                    <p>
                                      <?php echo $description ?>
                                    </p>
                                  </div>
                                </div>
                                <div class="row mb-2">
                                  <?php if (!empty($link) && $link != 'null') {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="link card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $link ?>" target="_blank" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $link ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              LINK
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                  <?php if (!empty($filePath) && !empty($file)) {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="file card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $filePath ?>" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $file ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              <?php echo strtoupper(pathinfo($file, PATHINFO_EXTENSION)); ?>
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                  <?php if (!empty($youtube) && $youtube != 'null') {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="youtube card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $youtube ?>" target="_blank" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $youtube ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              YOUTUBE LINK
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                </div>
                              </div>
                              <div class="card-footer" style="border: 1px solid #ccc; 
                              background-color: transparent; border-radius: 0%; padding: 10px;">
                                <a href="#" style="color: green; margin-left: 8px; text-decoration: none;">
                                  View Material
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                        $counter++;
                      }
                      foreach ($stmt_titles_assignment as $titlerow) {
                        $assignment_id = $titlerow['assignment_id'];
                        $title = $titlerow['title'];
                        $instruction = $titlerow['instruction'];
                        $date = $titlerow['date'];
                        $formattedDate = date('F j', strtotime($date));
                        $due_date = $titlerow['due_date'];
                        $formattedDueDate = date("F j", strtotime($due_date));
                        $words = explode(' ', $title);
                        $maxWords = 4;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $collapseID = "collapseAssignment" . $counter;
                        $link = $titlerow['link'];
                        $file = $titlerow['file'];
                        $fileDirectory = "assets/uploads/";
                        $filePath = $fileDirectory . $file;
                        $youtube = $titlerow['youtube'];

                        if (count($words) > $maxWords) {
                          $truncatedTitle .= '...';
                        }
                        ?>
                        <div class="topic-assignment">
                          <div class="col-12 grid-margin strech-card">
                            <div class="body-card"
                              style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                              <button class="d-flex justify-content-between align-items-center" data-toggle="collapse"
                                data-target="#<?php echo $collapseID ?>"
                                style="width: 100%; height: 100%; border: none; background-color: transparent;">
                                <div style="display: flex; align-items: center;">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-journal-text"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 10px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="ml-auto">
                                  <p class="text-body-secondary" style="margin-top: 10px;">Due
                                    <?php echo $formattedDueDate ?>
                                  </p>
                                </div>
                              </button>
                              <div class="dropdown">
                                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                  style="font-size: 20px; color: green; margin-right: 10px;">
                                  <i class="bi bi-three-dots-vertical"></i>
                                </a>

                                <ul class="dropdown-menu">
                                  <li>
                                    <a class="dropdown-item"
                                      href="edit_assignment.php?updateid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_assignment.php?deleteid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div id="<?php echo $collapseID ?>" class="collapse" aria-labelledby="accordionHeading">
                              <div class="card-body" style="border: 1px solid #ccc;">
                                <div class="row mb-2">
                                  <div class="col-md-8">
                                    <p class="text-body-secondary">Posted
                                      <?php echo $formattedDate ?>
                                    </p>
                                    <p>
                                      <?php echo $instruction ?>
                                    </p>
                                  </div>
                                </div>
                                <div class="row mb-2">
                                  <?php if (!empty($link) && $link != 'null') {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="link card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $link ?>" target="_blank" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $link ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              LINK
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                  <?php if (!empty($filePath) && !empty($file)) {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="file card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $filePath ?>" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $file ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              <?php echo strtoupper(pathinfo($file, PATHINFO_EXTENSION)); ?>
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                  <?php if (!empty($youtube) && $youtube != 'null') {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="youtube card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $youtube ?>" target="_blank" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $youtube ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              YOUTUBE LINK
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                </div>
                              </div>
                              <div class="card-footer" style="border: 1px solid #ccc; 
                              background-color: transparent; border-radius: 0%;">
                                <a href="assignment_review.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>" 
                                style="color: green; margin-left: 8px; text-decoration: none;">
                                  View Assignment
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                        $counter++;
                      }
                      foreach ($stmt_titles_question as $rowquestion) {
                        $question_id = $rowquestion['question_id'];
                        $title = $rowquestion['title'];
                        $instruction = $rowquestion['instruction'];
                        $date = $rowquestion['date'];
                        $formattedDate = date('F j', strtotime($date));
                        $due_date = $rowquestion['due_date'];
                        $formattedDueDate = date("F j", strtotime($due_date));
                        $words = explode(' ', $title);
                        $maxWords = 4;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $collapseID = "collapseQuestion" . $counter;
                        $link = $rowquestion['link'];
                        $file = $rowquestion['file'];
                        $fileDirectory = "assets/uploads/";
                        $filePath = $fileDirectory . $file;
                        $youtube = $rowquestion['youtube'];

                        if (count($words) > $maxWords) {
                          $truncatedTitle .= '...';
                        }
                        ?>
                        <div class="topic-question">
                          <div class="col-12 grid-margin strech-card">
                            <div class="body-card"
                              style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                              <button class="d-flex justify-content-between align-items-center" data-toggle="collapse"
                                data-target="#<?php echo $collapseID ?>"
                                style="width: 100%; height: 100%; border: none; background-color: transparent;">
                                <div style="display: flex; align-items: center;" id="accordionHeading">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-question-square"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 15px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="ml-auto">
                                  <p class="text-body-secondary" style="margin-top: 12px;">Due
                                    <?php echo $formattedDueDate ?>
                                  </p>
                                </div>
                              </button>
                              <div class="dropdown">
                                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                  style="font-size: 20px; color: green; margin-right: 10px;">
                                  <i class="bi bi-three-dots-vertical"></i>
                                </a>

                                <ul class="dropdown-menu">
                                  <li>
                                    <a class="dropdown-item"
                                      href="edit_question.php?updateid=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_question.php?deleteid=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div id="<?php echo $collapseID ?>" class="collapse" aria-labelledby="accordionHeading">
                              <div class="card-body" style="border: 1px solid #ccc;">
                                <div class="row mb-2">
                                  <div class="col-md-8">
                                    <p class="text-body-secondary">Posted
                                      <?php echo $formattedDate ?>
                                    </p>
                                    <p>
                                      <?php echo $instruction ?>
                                    </p>
                                  </div>
                                </div>
                                <div class="row mb-2">
                                  <?php if (!empty($link) && $link != 'null') {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="link card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $link ?>" target="_blank" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $link ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              LINK
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                  <?php if (!empty($filePath) && !empty($file)) {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="file card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $filePath ?>" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $file ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              <?php echo strtoupper(pathinfo($file, PATHINFO_EXTENSION)); ?>
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                  <?php if (!empty($youtube) && $youtube != 'null') {
                                    ?>
                                    <div class="col-md-4">
                                      <div class="youtube card" style="background-color: white; border: 1px solid #ccc;">
                                        <a href="<?php echo $youtube ?>" target="_blank" style="text-decoration: none;">
                                          <div class="row mt-3 ml-2">
                                            <div class="col-md-11">
                                              <p style="color: green; white-space: nowrap; overflow: hidden; 
                                                  text-overflow: ellipsis; max-width: 100%;">
                                                <?php echo $youtube ?>
                                              </p>
                                            </div>
                                          </div>
                                          <div class="row mb-3 ml-2">
                                            <div class="col-md-8 text-body-secondary">
                                              YOUTUBE LINK
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                    </div>
                                    <?php
                                  }
                                  ?>
                                </div>
                              </div>
                              <div class="card-footer" style="border: 1px solid #ccc; 
                              background-color: transparent; border-radius: 0%;">
                                <a href="question_review.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $question_id?>"
                                style="color: green; margin-left: 8px; text-decoration: none;">
                                  View Question
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                        $counter++;
                      }
                      foreach($stmt_titles_quiz as $rowQuiz) {
                        $quiz_id = $rowQuiz['quiz_id'];
                        $quizTitle = $rowQuiz['quizTitle'];
                        $quizInstruction = $rowQuiz['quizInstruction'];
                        $date = $rowQuiz['date'];
                        $formattedDate = date('F j', strtotime($date));
                        $dueDate = $rowQuiz['dueDate'];
                        $formattedDueDate = date("F j", strtotime($dueDate));
                        $words = explode(' ', $quizTitle);
                        $maxWords = 4;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $collapseID = "collapseQuestion" . $counter;

                        if (count($words) > $maxWords) {
                          $truncatedTitle .= '...';
                        }
                        ?>
                          <div class="topic-question">
                          <div class="col-12 grid-margin strech-card">
                            <div class="body-card"
                              style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                              <button class="d-flex justify-content-between align-items-center" data-toggle="collapse"
                                data-target="#<?php echo $collapseID ?>"
                                style="width: 100%; height: 100%; border: none; background-color: transparent;">
                                <div style="display: flex; align-items: center;" id="accordionHeading">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-card-list"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 15px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="ml-auto">
                                  <p class="text-body-secondary" style="margin-top: 12px;">Due
                                    <?php echo $formattedDueDate ?>
                                  </p>
                                </div>
                              </button>
                              <div class="dropdown">
                                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                  style="font-size: 20px; color: green; margin-right: 10px;">
                                  <i class="bi bi-three-dots-vertical"></i>
                                </a>

                                <ul class="dropdown-menu">
                                  <li>
                                    <a class="dropdown-item"
                                      href="edit_quiz.php?updateid=<?php echo $quiz_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_quiz.php?deleteid=<?php echo $quiz_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div id="<?php echo $collapseID ?>" class="collapse" aria-labelledby="accordionHeading">
                              <div class="card-body" style="border: 1px solid #ccc;">
                                <div class="row mb-2">
                                  <div class="col-md-8">
                                    <p class="text-body-secondary">Posted
                                      <?php echo $formattedDate ?>
                                    </p>
                                    <p>
                                      <?php echo $quizInstruction ?>
                                    </p>
                                  </div>
                                </div>
                              </div>
                              <div class="card-footer" style="border: 1px solid #ccc; 
                              background-color: transparent; border-radius: 0%;">
                                <a href="quiz_review.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $quiz_id?>"
                                style="color: green; margin-left: 8px; text-decoration: none;">
                                  View Quiz
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                        $counter++;
                      }
                      foreach($stmt_titles_exam as $rowExam) {
                        $exam_id = $rowExam['exam_id'];
                        $examTitle = $rowExam['examTitle'];
                        $examInstruction = $rowExam['examInstruction'];
                        $date = $rowExam['date'];
                        $formattedDate = date('F j', strtotime($date));
                        $dueDate = $rowExam['dueDate'];
                        $formattedDueDate = date("F j", strtotime($dueDate));
                        $words = explode(' ', $examTitle);
                        $maxWords = 4;
                        $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                        $collapseID = "collapseQuestion" . $counter;

                        if (count($words) > $maxWords) {
                          $truncatedTitle .= '...';
                        }
                        ?>
                          <div class="topic-question">
                          <div class="col-12 grid-margin strech-card">
                            <div class="body-card"
                              style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                              <button class="d-flex justify-content-between align-items-center" data-toggle="collapse"
                                data-target="#<?php echo $collapseID ?>"
                                style="width: 100%; height: 100%; border: none; background-color: transparent;">
                                <div style="display: flex; align-items: center;" id="accordionHeading">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-card-list"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 15px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="ml-auto">
                                  <p class="text-body-secondary" style="margin-top: 12px;">Due
                                    <?php echo $formattedDueDate ?>
                                  </p>
                                </div>
                              </button>
                              <div class="dropdown">
                                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                  style="font-size: 20px; color: green; margin-right: 10px;">
                                  <i class="bi bi-three-dots-vertical"></i>
                                </a>

                                <ul class="dropdown-menu">
                                  <li>
                                    <a class="dropdown-item"
                                      href="edit_exam.php?updateid=<?php echo $exam_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_exam.php?deleteid=<?php echo $exam_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div id="<?php echo $collapseID ?>" class="collapse" aria-labelledby="accordionHeading">
                              <div class="card-body" style="border: 1px solid #ccc;">
                                <div class="row mb-2">
                                  <div class="col-md-8">
                                    <p class="text-body-secondary">Posted
                                      <?php echo $formattedDate ?>
                                    </p>
                                    <p>
                                      <?php echo $examInstruction ?>
                                    </p>
                                  </div>
                                </div>
                              </div>
                              <div class="card-footer" style="border: 1px solid #ccc; 
                              background-color: transparent; border-radius: 0%;">
                                <a href="exam_review.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $exam_id?>"
                                style="color: green; margin-left: 8px; text-decoration: none;">
                                  View Exam
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                        $counter++;
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {
        $(".body-card button").click(function () {
          // Toggle the background color between #ccc and transparent
          if ($(this).css("background-color") === "rgba(0, 0, 0, 0)") {
            $(this).css("background-color", "#ccc");
          } else {
            $(this).css("background-color", "transparent");
          }
        });
      });
    </script>
    <script>
      var form = document.getElementById('myForm');
      var classTopicInput = document.getElementById('class_topic');
      var validationAlert = document.getElementById('validationAlert');

      form.addEventListener('submit', function (event) {
        // Check if class_topic field is empty
        if (classTopicInput.value.trim() === '') {
          event.preventDefault(); // Prevent form submission
          validationAlert.style.display = 'block'; // Show error message
          classTopicInput.classList.add('is-invalid'); // Add the is-invalid class to highlight the field
        }
      });

      // Add an event listener to hide the validation message when the user starts typing
      classTopicInput.addEventListener('input', function () {
        validationAlert.style.display = 'none'; // Hide the error message
        classTopicInput.classList.remove('is-invalid'); // Remove the is-invalid class
      });

      var form = document.getElementById('myEditForm');
      var editedClassTopic = document.getElementById('rename_topic');
      var editedValidationAlert = document.getElementById('editedValidationAlert');

      form.addEventListener('submit', function (event) {
        if (editedClassTopic.value.trim() === '') {
          event.preventDefault();
          editedValidationAlert.style.display = 'block';
          editedClassTopic.classList.add('is-invalid');
        }
      });

      editedClassTopic.addEventListener('input', function () {
        editedValidationAlert.style.display = 'none';
        editedClassTopic.classList.remove('is-invalid');
      });
    </script>
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