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
        <div class="header-sticky" style="overflow-x: auto; white-space: nowrap;">
          <div class="header-links">
            <?php
            if (isset($_GET['class_id'])) {
              $class_id = $_GET['class_id'];
              ?>
              <a class="btn-success" href="course.php"><i class="bi bi-arrow-bar-left" style="color: white;"></i></a>
              <a href="class_course.php?class_id=<?php echo $class_id ?>" class="people"
                style="margin-left: 2vh;">Stream</a>
              <a href="class_classwork.php?class_id=<?php echo $class_id ?>" class="people">Classwork</a>
              <a href="class_people.php?class_id=<?php echo $class_id ?>" class="people">People</a>
              <a href="class_grade.php?class_id=<?php echo $class_id ?>" class="nav-link active">Grade</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper" style="margin-top: 10vh;">
          <div id="print-content">
            <div class="row">
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
                            <table id="example" class="table table-bordered table-hover text-center"
                              style="width: 100%; table-layout: fixed; border-collapse: collapse;">
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

                                $sqlQuiz = "SELECT quizTitle as title, date, quizPoint as point, 'Quiz' as type FROM classwork_quiz 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuiz = $db->prepare($sqlQuiz);
                                $stmtQuiz->execute([$tc_id, $teacher_id]);
                                $quizTitles = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

                                $sqlExam = "SELECT examTitle as title, date, examPoint as point, 'Exam' as type FROM classwork_exam 
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
                                    <p style="color: white;">out of
                                      <?php echo $title['point']; ?>
                                    </p>
                                  </th>
                                  <?php
                                }
                                ?>
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

                                      $sqlQuestionScore = "SELECT score FROM questiongrade WHERE student_id = ? AND questionTitle = ?";
                                      $stmtQuestionScore = $db->prepare($sqlQuestionScore);
                                      $stmtQuestionScore->execute([$student_id, $questionTitle]);
                                      $questionScore = $stmtQuestionScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlAssignmentScore = "SELECT score FROM assignmentgrade WHERE student_id = ? AND assignmentTitle = ?";
                                      $stmtAssignmentScore = $db->prepare($sqlAssignmentScore);
                                      $stmtAssignmentScore->execute([$student_id, $assignmentTitle]);
                                      $assignmentScore = $stmtAssignmentScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlQuizScore = "SELECT score FROM quizgrade WHERE student_id = ? AND quizTitle = ?";
                                      $stmtQuizScore = $db->prepare($sqlQuizScore);
                                      $stmtQuizScore->execute([$student_id, $quizTitle]);
                                      $quizScore = $stmtQuizScore->fetch(PDO::FETCH_ASSOC);

                                      $sqlExamScore = "SELECT score FROM examgrade WHERE student_id = ? AND examTitle = ?";
                                      $stmtExamScore = $db->prepare($sqlExamScore);
                                      $stmtExamScore->execute([$student_id, $examTitle]);
                                      $examScore = $stmtExamScore->fetch(PDO::FETCH_ASSOC);

                                      if ($questionScore && $assignmentScore && $quizScore) {
                                        ?>
                                        <td>
                                          <?php echo $questionScore['score'] . "<br> " . $assignmentScore['score'] . "<br> " . $quizScore['score'] ?>
                                        </td>
                                        <?php
                                      } elseif ($questionScore && $assignmentScore) {
                                        ?>
                                        <td>
                                          <?php echo $questionScore['score'] . "<br> " . $assignmentScore['score'] ?>
                                        </td>
                                        <?php
                                      } elseif ($questionScore && $quizScore) {
                                        ?>
                                        <td>
                                          <?php echo $questionScore['score'] . "<br> " . $quizScore['score'] ?>
                                        </td>
                                        <?php
                                      } elseif ($assignmentScore && $quizScore) {
                                        ?>
                                        <td>
                                          <?php echo $assignmentScore['score'] . "<br> " . $quizScore['score'] ?>
                                        </td>
                                        <?php
                                      } elseif ($questionScore) {
                                        ?>
                                        <td>
                                          <?php echo $questionScore['score'] ?>
                                        </td>
                                        <?php
                                      } elseif ($assignmentScore) {
                                        ?>
                                        <td>
                                          <?php echo $assignmentScore['score'] ?>
                                        </td>
                                        <?php
                                      } elseif ($quizScore) {
                                        ?>
                                        <td>
                                          <?php echo $quizScore['score'] ?>
                                        </td>
                                        <?php
                                      } else {
                                        ?>
                                        <td></td>
                                        <?php
                                      }
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