<?php
session_start();
include("config.php");
$teacher_id = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$profile = $stmt->fetchColumn();

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
}

if (isset($_GET['assignment_id'])) {
  $assignment_id = $_GET['assignment_id'];
}

$sql_selectAssignment = "SELECT * FROM classwork_assignment WHERE assignment_id = ? AND class_id = ? AND teacher_id = ?";
$stmt_selectAssignment = $db->prepare($sql_selectAssignment);
$stmt_selectAssignment->execute([$assignment_id, $class_id, $teacher_id]);
$result = $stmt_selectAssignment->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $assignmentRow) {
  $title = $assignmentRow['title'];
  $instruction = $assignmentRow['instruction'];
  $type = $assignmentRow['type'];
  $point = $assignmentRow['point'];
  $date = $assignmentRow['date'];
  $assignmentStatus = $assignmentRow['assignment_status'];
}

$sql_getStudents = "SELECT * FROM class_enrolled WHERE tc_id = ? AND teacher_id = ? ORDER BY student_firstname ASC";
$stmt_getStudents = $db->prepare($sql_getStudents);
$stmt_getStudents->execute([$class_id, $teacher_id]);
$result = $stmt_getStudents->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $studentRow) {
  $student_id = $studentRow['student_id'];
  $student_firstname = $studentRow['student_firstname'];
  $student_lastname = $studentRow['student_lastname'];
  $class_name = $studentRow['class_name'];

  $sql_selectProfile = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
  $stmt_selectProfile = $db->prepare($sql_selectProfile);
  $stmt_selectProfile->execute([$student_id]);
  $otherProfile = $stmt_selectProfile->fetchColumn();
}

if (isset($_POST['assignmentGrade'])) {
  $assignmentTitle = $_POST['assignmentTitle'];
  $studentFirstName = $_POST['studentFirstName'];
  $studentLastName = $_POST['studentLastName'];
  $score = $_POST['score'];
  $assignmentPoint = $_POST['assignmentPoint'];
  $student_id = $_POST['student_id'];

  $sql_assignmentGrade = "INSERT INTO assignmentgrade (assignmentTitle, studentFirstName, studentLastname, date, gradeType, score, 
  assignmentPoint, student_id, teacher_id, class_id, assignment_id) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
  $stmt_assignmentGrade = $db->prepare($sql_assignmentGrade);
  $assignmentGradeResult = $stmt_assignmentGrade->execute([$assignmentTitle, $studentFirstName, 
  $studentLastName, $type, $score, $assignmentPoint, $student_id, $teacher_id, $class_id, $assignment_id]);
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
  <link rel="stylesheet" href="assets/css/my_student.css">
  <link rel="stylesheet" href="assets/css/notification.css">
  <link rel="shortcut icon" href="assets/image/trace.svg" />
</head>

<body>
  <div class="container-scroller">
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
                        <?php
                        $link = '';

                        $currentDate = new DateTime();
                        $endDate = new DateTime($notification['end_date']);
                        if ($currentDate > $endDate) {
                          $link = 'index.php';
                        } else {
                          if ($notification['type'] === 'announcement') {
                            $link = 'view_announcement.php' . $notification['news_id'];
                          } elseif ($notification['type'] === 'news') {
                            $link = 'view_news.php?news_id=' . $notification['news_id'];
                          }
                        }
                        ?>
                        <h6 class="preview-subject font-weight-normal"
                          onclick="window.location.href='<?php echo $link; ?>';">
                          <?php echo $notification['title']; ?> (
                          <?php echo ucfirst($notification['type']); ?>)
                        </h6>
                        <p class="font-weight-light small-text mb-0 text-muted"
                        onclick="window.location.href='<?php echo $link; ?>';">
                          by
                          <?php echo $notification['name']; ?> on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['student_id'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['student_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal" 
                        onclick="window.location.href='student.php'">
                          You added
                          <?php echo $studentName; ?> as student.
                        </h6>
                        <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='student.php'">
                          on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['class_name'])): ?>
                        <div class="preview-item-content"
                          onclick="window.location.href='class_people.php?class_id=<?php echo $notification['tc_id'] ?>'">
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
                          <h6 class="preview-subject font-weight-normal"
                          onclick="window.location.href='question_review.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $notification['question_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['question_course_status']; ?>
                          <?php echo $notification['title']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='question_review.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $notification['question_id'] ?>'">
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
                        <h6 class="preview-subject font-weight-normal"
                        onclick="window.location.href='assignment_review.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $notification['assignment_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['assignment_course_status']; ?>
                          <?php echo $notification['title']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='assignment_review.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $notification['assignment_id'] ?>'">
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
                        <h6 class="preview-subject font-weight-normal"
                        onclick="window.location.href='quiz_review.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $notification['quiz_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['quiz_course_status']; ?>
                          <?php echo $notification['quizTitle']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='quiz_review.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $notification['quiz_id'] ?>'">
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
                        <h6 class="preview-subject font-weight-normal"
                        onclick="window.location.href='exam_review.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $notification['exam_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['exam_course_status']; ?>
                          <?php echo $notification['examTitle']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='exam_review.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $notification['exam_id'] ?>'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
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
        <div class="content-wrapper">
          <div class="row mb-4">
            <div class="col">
              <h2>
                <?php echo $class_name ?>
              </h2>
              <p class="text-body-secondary">(Assignment Review)</p>
              <a href="toreview.php?class_id=<?php echo $class_id ?>" style="text-decoration: none; color: green;">
                Go back to to-review page.
              </a>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="ml-4 mt-3 mb-3" s>
                  <h2>
                    <?php echo $title ?>
                  </h2>
                  <p class="text-body-secondary">Instructions:
                    <?php echo $instruction ?>
                  </p>
                </div>
                <div class="row">
                  <?php
                  foreach ($result as $studentRow) {
                    $student_id = $studentRow['student_id'];
                    $student_firstname = $studentRow['student_firstname'];
                    $student_lastname = $studentRow['student_lastname'];
                    $class_name = $studentRow['class_name'];

                    $sql_selectProfile = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
                    $stmt_selectProfile = $db->prepare($sql_selectProfile);
                    $stmt_selectProfile->execute([$student_id]);
                    $profileResult = $stmt_selectProfile->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($profileResult as $profileRow) {
                      $otherProfile = $profileRow['profile'];
                      ?>
                      <div class="col-md-3 mb-4">
                        <div class="card card-tale justify-content-center align-items-center" data-bs-toggle="modal"
                          data-bs-target="#staticBackdrop_<?php echo $student_id; ?>" style="cursor: pointer;">
                          <div class="circle-image mt-4 mb-3">
                            <img src="../student/assets/image/<?php echo $otherProfile; ?>" alt="Circular Image">
                          </div>
                          <p class="text-body-secondary">
                            <?php echo $student_firstname . ' ' . $student_lastname ?>
                          </p>
                        </div>
                      </div>
                      <div class="modal fade" id="staticBackdrop_<?php echo $student_id; ?>" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1"
                        aria-labelledby="staticBackdropLabel_<?php echo $student_id; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                          <form action="" method="post">
                            <div class="modal-content">
                              <div class="modal-header" style="border: none; margin-bottom: -40px;">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel_<?php echo $student_id; ?>">
                                  <input type="hidden" name="student_id" value="<?php echo $student_id ?>">
                                  <?php echo $title ?> (
                                  <?php echo $date ?>)
                                  <input type="hidden" name="assignmentTitle" value="<?php echo $title ?>">
                                  <p class="text-body-secondary">by
                                    <?php echo $student_firstname . ' ' . $student_lastname ?>
                                    <input type="hidden" name="studentFirstName" value="<?php echo $student_firstname ?>">
                                    <input type="hidden" name="studentLastName" value="<?php echo $student_lastname ?>">
                                  </p>
                                  <?php
                                  $sqlAssignmentAnswer = "SELECT assignment_course_status FROM student_assignment_course_answer WHERE user_id = ?
                                    AND assignment_id = ?";
                                  $stmtAssignmentAnswer = $db->prepare($sqlAssignmentAnswer);
                                  $stmtAssignmentAnswer->execute([$student_id, $assignment_id]);
                                  $assignmentResult = $stmtAssignmentAnswer->fetchAll(PDO::FETCH_ASSOC);

                                  $hasTurnedInStatus = false;

                                  foreach ($assignmentResult as $assignmentRow) {
                                    $assignmentCourseStatus = $assignmentRow['assignment_course_status'];

                                    if ($assignmentCourseStatus === 'turned in' || $assignmentCourseStatus === 'turned-in late') {
                                      $hasTurnedInStatus = true;
                                      break;
                                    }
                                  }

                                  $statusColorClass = '';

                                  if (!$hasTurnedInStatus) {
                                    $statusColorClass = ($assignmentStatus === 'assigned') ? 'text-success' : 'text-danger';
                                  }

                                  if (!$hasTurnedInStatus) {
                                    ?>
                                    <p class="text-body-secondary mt-1 <?php echo $statusColorClass; ?>">
                                      <?php echo ucfirst($assignmentStatus) ?>
                                    </p>
                                    <?php
                                  }
                                  ?>
                                  <?php
                                  $sql_assignmentScore = "SELECT score FROM assignmentgrade WHERE student_id = ? AND assignment_id = ?";
                                  $stmt_assignmentScore = $db->prepare($sql_assignmentScore);
                                  $stmt_assignmentScore->execute([$student_id, $assignment_id]);
                                  $assignmentScoreResult = $stmt_assignmentScore->fetch(PDO::FETCH_ASSOC);

                                  if (empty($assignmentScoreResult)) {
                                    ?>
                                    <p>
                                      <input type="text" name="score" style=" height: 4vh; width: 4vh; font-size: 13px; border: none;
                                    border-bottom: 1px solid #ccc; margin-bottom: 0; padding-bottom: 0;">
                                      /
                                      <?php echo $point ?>
                                      <input type="hidden" name="assignmentPoint" value="<?php echo $point ?>">
                                    </p>
                                    <?php
                                  } else {
                                    $assignmentScore = $assignmentScoreResult['score'];
                                    ?>
                                    <p>
                                      <input type="text" name="score" style=" height: 4vh; width: 4vh; font-size: 13px; border: none;
                                      border-bottom: 1px solid #ccc; margin-bottom: 0; padding-bottom: 0;"
                                        value="<?php echo $assignmentScore ?>" readonly>
                                      /
                                      <?php echo $point ?>
                                      <input type="hidden" name="assignmentPoint" value="<?php echo $point ?>">
                                    </p>
                                    <?php
                                  }
                                  ?>
                                </h1>
                              </div>
                              <div class="modal-body">
                                <?php
                                $sqlAssignmentAnswer = "SELECT * FROM student_assignment_course_answer WHERE user_id = ?
                                AND assignment_id = ?";
                                $stmtAssignmentAnswer = $db->prepare($sqlAssignmentAnswer);
                                $stmtAssignmentAnswer->execute([$student_id, $assignment_id]);
                                $assignmentResult = $stmtAssignmentAnswer->fetchAll(PDO::FETCH_ASSOC);

                                $hasTurnedInStatus = false;

                                foreach ($assignmentResult as $assignmentRow) {
                                  $assignmentLink = $assignmentRow['assignment_link'];
                                  $assignmentFile = $assignmentRow['assignment_file'];
                                  $fileDirectory = "assets/uploads/";
                                  $filePath = $fileDirectory . $assignmentFile;
                                  $assignmentCourseStatus = $assignmentRow['assignment_course_status'];
                                  $statusColor = ($assignmentCourseStatus === 'turned in') ? 'green' : 'red';
                                  ?>
                                  <span style="color: <?php echo $statusColor; ?>">
                                    <?php echo ucfirst($assignmentCourseStatus) ?>
                                  </span>
                                  <p class="text-body-secondary mt-2">Student Work/s</p>
                                  <div class="row mt-2">
                                    <?php if (!empty($assignmentLink) && $assignmentLink != 'null') {
                                      ?>
                                      <div class="col-md-6">
                                        <div class="card" style="border: 1px solid #ccc; border-radius: 5%;">
                                          <a class="mt-3 ml-3" href="<?php echo $assignmentLink ?>" target="_blank"
                                            style="text-decoration: none;">
                                            <div class="row">
                                              <div class="col">
                                                <p
                                                  style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90%;">
                                                  <?php echo $assignmentLink ?>
                                                </p>
                                              </div>
                                            </div>
                                            <div class="row mb-3">
                                              <div class="col text-body-secondary">
                                                LINK
                                              </div>
                                            </div>
                                          </a>
                                        </div>
                                      </div>
                                      <?php
                                    }
                                    ?>
                                    <?php if (!empty($filePath) && !empty($assignmentFile)) {
                                      ?>
                                      <div class="col-md-6">
                                        <div class="card" style="border: 1px solid #ccc; border-radius: 5%;">
                                          <a class="mt-3 ml-3" href="<?php echo $filePath ?>" style="text-decoration: none;">
                                            <div class="row">
                                              <div class="col">
                                                <p
                                                  style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                  <?php echo $assignmentFile ?>
                                                </p>
                                              </div>
                                            </div>
                                            <div class="row mb-3">
                                              <div class="col text-body-secondary">
                                                <?php echo strtoupper(pathinfo($assignmentFile, PATHINFO_EXTENSION)); ?>
                                              </div>
                                            </div>
                                          </a>
                                        </div>
                                      </div>
                                      <?php
                                    }
                                    ?>
                                  </div>
                                  <?php
                                }
                                ?>
                              </div>
                              <div class="modal-footer" style="border: none;">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <?php
                                if (empty($assignmentScoreResult)) {
                                  ?>
                                  <button type="submit" name="assignmentGrade" class="btn btn-success">Submit</button>
                                  <?php
                                } 
                                ?>
                              </div>
                            </div>
                          </form>
                        </div>
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