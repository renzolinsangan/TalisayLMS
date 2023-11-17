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
  <link rel="stylesheet" href="assets/css/class_cw.css">
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
          <div class="header-links" style="overflow-y: auto; white-space: nowrap;">
            <?php
            if (isset($_GET['class_id'])) {
              $class_id = $_GET['class_id'];
              ?>
              <a class="btn-success" href="archive.php"><i class="bi bi-arrow-bar-left" style="color: white;"></i></a>
              <a href="archive_classCourse.php?class_id=<?php echo $class_id ?>" class="people"
                style="margin-left: 2vh;">Stream</a>
              <a href="archive_classClasswork.php?class_id=<?php echo $class_id ?>" class="nav-link active">Classwork</a>
              <a href="archive_classPeople.php?class_id=<?php echo $class_id ?>" class="people">People</a>
              <a href="archive_classGrade.php?class_id=<?php echo $class_id ?>" class="people">Grade</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12" style="margin-top: 10vh;">
              <div class="row">
                <?php
                include("db_conn.php");
                $sql = "SELECT class_topic FROM topic WHERE teacher_id=? AND class_name=?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$teacher_id, $class_name]);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $_SESSION['class_topic'] = $row['class_topic'];
                  ?>
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card" style="border-radius: 0px;">
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
                              <a class="dropdown-item" href="#">Copy Link</a>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <p class="bottom-border"></p>
                      <div class="topic-body">
                        <?php
                        $counter = 1;

                        $sql_material = "SELECT material_id, title, description, date, link, file, youtube 
                          FROM classwork_material WHERE class_topic=? AND teacher_id=? AND class_name=?";
                        $stmt_titles_material = $db->prepare($sql_material);
                        $stmt_titles_material->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

                        $sql_assignment = "SELECT assignment_id, title, instruction, date, due_date, link, file, youtube
                          FROM classwork_assignment WHERE class_topic=? AND teacher_id=? AND class_name=?";
                        $stmt_titles_assignment = $db->prepare($sql_assignment);
                        $stmt_titles_assignment->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

                        $sql_question = "SELECT question_id, title, instruction, date, due_date, link, file, youtube 
                          FROM classwork_question WHERE class_topic=? AND teacher_id=? AND class_name=?";
                        $stmt_titles_question = $db->prepare($sql_question);
                        $stmt_titles_question->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

                        $sql_quiz = "SELECT quiz_id, quizTitle, quizInstruction, quizLink, date, dueDate 
                        FROM classwork_quiz WHERE classTopic=? AND teacher_id=? AND class_name=?";
                        $stmt_titles_quiz = $db->prepare($sql_quiz);
                        $stmt_titles_quiz->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

                        $sql_exam = "SELECT exam_id, examTitle, examInstruction, examLink, date, dueDate
                        FROM classwork_exam WHERE classTopic = ? AND teacher_id=? AND class_name=?";
                        $stmt_titles_exam = $db->prepare($sql_exam);
                        $stmt_titles_exam->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

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
                          $fileDirectory = "../teacher/assets/uploads/";
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
                                    <p class="text-body-secondary" style="margin-top: 12px;">
                                      Posted
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
                                      <a class="dropdown-item" href="">Copy Link</a>
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
                                          <a href="<?php echo $link ?>" target="_blank"
                                            style="text-decoration: none;">
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
                                          <a href="<?php echo $youtube ?>" target="_blank"
                                            style="text-decoration: none;">
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
                                  <a href="archive_materialCourse.php?class_id=<?php echo $class_id ?>&material_id=<?php echo $material_id ?>"
                                    style="color: green; margin-left: 8px; text-decoration: none;">
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
                          $fileDirectory = "../teacher/assets/uploads/";
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
                                    <p style="margin-top: 12px; margin-left: 30px; font-size: 17px; color: black;">
                                      <?php echo $truncatedTitle ?>
                                    </p>
                                  </div>
                                  <div class="ml-auto">
                                    <p class="text-body-secondary" style="margin-top: 12px;">
                                      Due
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
                                      <a class="dropdown-item" href="">Copy Link</a>
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
                                          <a href="<?php echo $link ?>" target="_blank"
                                            style="text-decoration: none;">
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
                                          <a href="<?php echo $youtube ?>" target="_blank"
                                            style="text-decoration: none;">
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
                                  <a href="archive_assignmentCourse.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>&user_id=<?php echo $user_id ?>"
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
                          $fileDirectory = "../teacher/assets/uploads/";
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
                                  <div style="display: flex; align-items: center;">
                                    <div
                                      style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                      <i class="bi bi-question-square"
                                        style="color: white; line-height: 41px; font-size: 26px;"></i>
                                    </div>
                                    <p style="margin-top: 12px; margin-left: 30px; font-size: 17px; color: black;">
                                      <?php echo $truncatedTitle ?>
                                    </p>
                                  </div>
                                  <div class="ml-auto">
                                    <p class="text-body-secondary" style="margin-top: 12px;">
                                      Due
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
                                      <a class="dropdown-item" href="">Copy Link</a>
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
                                          <a href="<?php echo $link ?>" target="_blank"
                                            style="text-decoration: none;">
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
                                          <a href="<?php echo $youtube ?>" target="_blank"
                                            style="text-decoration: none;">
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
                                  <a href="archive_questionCourse.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $question_id ?>&user_id=<?php echo $user_id ?>"
                                    style="color: green; margin-left: 8px; text-decoration: none;">
                                    View Question
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                          <?php
                        }
                        foreach ($stmt_titles_quiz as $rowQuiz) {
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
                                        href="#">Copy Link</a>
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
                                  <a href="quiz_course.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $quiz_id ?>&user_id=<?php echo $user_id ?>"
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
                        foreach ($stmt_titles_exam as $rowExam) {
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
                                        href="#">Copy Link</a>
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
                                  <a href="exam_course.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $exam_id ?>&user_id=<?php echo $user_id ?>"
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
                  <?php
                  $counter++;
                }
                ?>
              </div>
            </div>
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
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="../../vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../../js/off-canvas.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/settings.js"></script>
    <script src="../../js/todolist.js"></script>
    <!-- endinject -->
</body>

</html>