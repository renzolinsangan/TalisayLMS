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

if (isset($_GET['quiz_id'])) {
  $quiz_id = $_GET['quiz_id'];
}

$sql_selectQuiz = "SELECT * FROM classwork_quiz WHERE quiz_id = ? AND class_id = ? AND teacher_id = ?";
$stmt_selectQuiz = $db->prepare($sql_selectQuiz);
$stmt_selectQuiz->execute([$quiz_id, $class_id, $teacher_id]);
$result = $stmt_selectQuiz->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $quizRow) {
  $quizTitle = $quizRow['quizTitle'];
  $quizInstruction = $quizRow['quizInstruction'];
  $quizLink = $quizRow['quizLink'];
  $quizPoint = $quizRow['quizPoint'];
  $date = $quizRow['date'];
  $type = $quizRow['type'];
  $quizStatus = $quizRow['quizStatus'];
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

if (isset($_POST['quizGrade'])) {
  $quizTitle = $_POST['quizTitle'];
  $studentFirstName = $_POST['studentFirstName'];
  $studentLastName = $_POST['studentLastName'];
  $score = $_POST['score'];
  $quizPoint = $_POST['quizPoint'];
  $student_id = $_POST['student_id'];

  $sql_assignmentGrade = "INSERT INTO quizgrade (quizTitle, studentFirstName, studentLastname, date, gradeType, score, 
  quizPoint, student_id, teacher_id, class_id, quiz_id) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
  $stmt_assignmentGrade = $db->prepare($sql_assignmentGrade);
  $assignmentGradeResult = $stmt_assignmentGrade->execute([
    $quizTitle,
    $studentFirstName,
    $studentLastName,
    $type,
    $score,
    $quizPoint,
    $student_id,
    $teacher_id,
    $class_id,
    $quiz_id
  ]);
}

if (isset($_POST['editGrade'])) {
  $score = $_POST['score'];
  $quiz_id = $_POST['quiz_id'];
  $student_id = $_POST['student_id'];

  $sqlEditPoint = "UPDATE quizgrade SET score = $score WHERE student_id = ? AND quiz_id = ?";
  $stmtEditPoint = $db->prepare($sqlEditPoint);
  $stmtEditPoint->execute([$student_id, $quiz_id]);
  header("Location: quiz_review.php?class_id=$class_id&quiz_id=$quiz_id");
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
                        <h6 class="preview-subject font-weight-normal" onclick="window.location.href='student.php'">
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
                    href="student_report.php?user_id=<?php echo $teacher_id ?>">Student Reports</a>
                </li>
          </li>
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
            <p class="text-body-secondary">(Quiz Review)</p>
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
                  <?php echo $quizTitle ?>
                </h2>
                <p class="text-body-secondary">Instructions:
                  <?php echo $quizInstruction ?>
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

                  $sqlQuizAnswer = "SELECT * FROM student_quiz_course_answer WHERE user_id = ? AND quiz_id = ?";
                  $stmtQuizAnswer = $db->prepare($sqlQuizAnswer);
                  $stmtQuizAnswer->execute([$student_id, $quiz_id]);
                  $quizResult = $stmtQuizAnswer->fetchAll(PDO::FETCH_ASSOC);

                  $hasTurnedInStatus = false;

                  foreach ($quizResult as $quizRow) {
                    $quizCourseStatus = $quizRow['quiz_course_status'];
                    if ($quizCourseStatus === 'turned in' || $quizCourseStatus === 'turned-in late') {
                      $hasTurnedInStatus = true;
                      break;
                    }
                  }

                  $statusColorClass = '';

                  if (!$hasTurnedInStatus) {
                    $statusColorClass = ($quizStatus === 'assigned') ? 'text-success' : 'text-danger';
                  }

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
                        <?php
                        $sqlQuizScore = "SELECT score FROM quizgrade WHERE student_id = ? AND quiz_id = ?";
                        $stmtQuizScore = $db->prepare($sqlQuizScore);
                        $stmtQuizScore->execute([$student_id, $quiz_id]);
                        $quizScoreResult = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);

                        if (empty($quizScoreResult)) {
?>
                        <form action="" method="post">
                          <div class="modal-content">
                            <div class="modal-header" style="border: none; margin-bottom: -40px;">
                              <h1 class="modal-title fs-5" id="staticBackdropLabel_<?php echo $student_id; ?>">
                                <input type="hidden" name="student_id" value="<?php echo $student_id ?>">
                                <?php echo $quizTitle ?> (
                                <?php echo $date ?>)
                                <input type="hidden" name="quizTitle" value="<?php echo $quizTitle ?>">
                                <p class="text-body-secondary">by
                                  <?php echo $student_firstname . ' ' . $student_lastname ?>
                                  <input type="hidden" name="studentFirstName" value="<?php echo $student_firstname ?>">
                                  <input type="hidden" name="studentLastName" value="<?php echo $student_lastname ?>">
                                </p>
                                <?php
                                if (!$hasTurnedInStatus) {
                                  ?>
                                  <p class="text-body-secondary mt-1 <?php echo $statusColorClass; ?>">
                                    <?php echo ucfirst($quizStatus) ?>
                                  </p>
                                  <?php
                                }
                                ?>
                                <?php
                                $sqlQuizScore = "SELECT score FROM quizgrade WHERE student_id = ? AND quiz_id = ?";
                                $stmtQuizScore = $db->prepare($sqlQuizScore);
                                $stmtQuizScore->execute([$student_id, $quiz_id]);
                                $quizScoreResult = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);

                                if (empty($quizScoreResult)) {
                                  ?>
                                  <p>
                                    <input type="text" name="score" style="height: 4vh; width: 4vh; font-size: 13px; border: none;
                        border-bottom: 1px solid #ccc; margin-bottom: 0; padding-bottom: 0;">
                                    /
                                    <?php echo $quizPoint; ?>
                                    <input type="hidden" name="quizPoint" value="<?php echo $quizPoint; ?>">
                                  </p>
                                  <?php
                                } else {
                                  $quizScore = $quizScoreResult['score'];
                                  ?>
                                  <p>
                                    <input type="text" name="score" style="height: 4vh; width: 4vh; font-size: 13px; border: none;
                        border-bottom: 1px solid #ccc; margin-bottom: 0; padding-bottom: 0;"
                                      value="<?php echo $quizScore; ?>" readonly>
                                    /
                                    <?php echo $quizPoint; ?>
                                    <input type="hidden" name="quizPoint" value="<?php echo $quizPoint; ?>">
                                  </p>
                                  <?php
                                }
                                ?>
                              </h1>
                            </div>
                            <div class="modal-body" style="margin-top: -10px;">
                              <?php foreach ($quizResult as $quizRow): ?>
                                <?php
                                $quizLink = $quizRow['quizLink'];
                                $quizCourseStatus = $quizRow['quiz_course_status'];
                                $statusColor = ($quizCourseStatus === 'turned in') ? 'green' : 'red';
                                ?>
                                <span style="color: <?php echo $statusColor; ?>">
                                  <?php echo ucfirst($quizCourseStatus) ?>
                                </span>
                                <p class="mt-1">This is the link that you provided in your student.
                                  You can check and provide score in the submitted quiz of the student.
                                </p>
                                <a href="<?php echo $quizLink ?>" target="_blank">
                                  <?php echo $quizLink ?>
                                </a>
                                <?php
                                ?>
                              <?php endforeach; ?>
                            </div>
                            <div class="modal-footer" style="border: none;">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <?php
                              if (empty($quizScoreResult)) {
                                ?>
                                <button type="submit" name="quizGrade" class="btn btn-success">Submit</button>
                                <?php
                              }
                              ?>
                            </div>
                          </div>
                        </form>
<?php
                        } else {
                          ?>
                        <form action="" method="post">
                          <div class="modal-content">
                            <div class="modal-header" style="border: none; margin-bottom: -40px;">
                              <h1 class="modal-title fs-5" id="staticBackdropLabel_<?php echo $student_id; ?>">
                                <input type="hidden" name="student_id" value="<?php echo $student_id ?>">
                                <?php echo $quizTitle ?> (
                                <?php echo $date ?>)
                                <input type="hidden" name="quizTitle" value="<?php echo $quizTitle ?>">
                                <p class="text-body-secondary">by
                                  <?php echo $student_firstname . ' ' . $student_lastname ?>
                                  <input type="hidden" name="studentFirstName" value="<?php echo $student_firstname ?>">
                                  <input type="hidden" name="studentLastName" value="<?php echo $student_lastname ?>">
                                </p>
                                <?php
                                if (!$hasTurnedInStatus) {
                                  ?>
                                  <p class="text-body-secondary mt-1 <?php echo $statusColorClass; ?>">
                                    <?php echo ucfirst($quizStatus) ?>
                                  </p>
                                  <?php
                                }
                                ?>
                                <?php
                                $sqlQuizScore = "SELECT score FROM quizgrade WHERE student_id = ? AND quiz_id = ?";
                                $stmtQuizScore = $db->prepare($sqlQuizScore);
                                $stmtQuizScore->execute([$student_id, $quiz_id]);
                                $quizScoreResult = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);

                                if (!empty($quizScoreResult)) {
                                  $quiz_id = $_GET['quiz_id'];
                                  $score = $quizScoreResult['score'];
                                  ?>
                                  <p>
                                    <input type="text" name="score" style="height: 4vh; width: 4vh; font-size: 13px; border: none;
                        border-bottom: 1px solid #ccc; margin-bottom: 0; padding-bottom: 0;" value="<?php echo $score ?>">
                                    /
                                    <?php echo $quizPoint; ?>
                                    <input type="hidden" name="student_id" value="<?php echo $student_id ?>">
                        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id ?>">
                                  </p>
                                  <?php
                                }
                                ?>
                              </h1>
                            </div>
                            <div class="modal-body" style="margin-top: -10px;">
                              <?php foreach ($quizResult as $quizRow): ?>
                                <?php
                                $quizLink = $quizRow['quizLink'];
                                $quizCourseStatus = $quizRow['quiz_course_status'];
                                $statusColor = ($quizCourseStatus === 'turned in') ? 'green' : 'red';
                                ?>
                                <span style="color: <?php echo $statusColor; ?>">
                                  <?php echo ucfirst($quizCourseStatus) ?>
                                </span>
                                <p class="mt-1">This is the link that you provided in your student.
                                  You can check and provide score in the submitted quiz of the student.
                                </p>
                                <a href="<?php echo $quizLink ?>" target="_blank">
                                  <?php echo $quizLink ?>
                                </a>
                                <?php
                                ?>
                              <?php endforeach; ?>
                            </div>
                            <div class="modal-footer" style="border: none;">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <?php
                              if (!empty($quizScoreResult)) {
                                ?>
                                <button type="submit" name="editGrade" class="btn btn-success">Edit</button>
                                <?php
                              }
                              ?>
                            </div>
                          </div>
                        </form>
                          <?php
                        }
                        ?>
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