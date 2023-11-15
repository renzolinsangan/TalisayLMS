<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location:../../user_login.php");
  exit();
}

if (isset($_GET['user_id'])) {
  $user_id = $_GET['user_id'];
}

include("db_conn.php");
$teacher_id = $_SESSION['user_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/student_report.css">
  <link rel="stylesheet" href="assets/css/notif.css">
  <link rel="shortcut icon" href="images/trace.svg" />
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
        <div class="header-links" style="overflow-x: auto; white-space: nowrap;">
          <?php
          include("config.php");

          $classFromUrl = isset($_GET['class_name']) ? urldecode($_GET['class_name']) : '';

          // Fetch distinct class_name and tc_id from the database
          $sql = "SELECT DISTINCT class_name, class_id FROM section WHERE teacher_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          $class_names_and_tc_ids = array();

          while ($row = $result->fetch_assoc()) {
            $class_names_and_class_ids[] = $row;
          }

          foreach ($class_names_and_class_ids as $class_data) {
            $class_name = $class_data['class_name'];
            $class_id = $class_data['class_id'];

            $safeClass = str_replace(' ', '_', $class_name);
            $cssClass = ($classFromUrl === $class_name) ? 'active' : '';

            $encodedClass = urlencode($class_name);

            echo '<a href="grade_report_subject.php?user_id=' . $teacher_id . '&class_name=' . $encodedClass . '&class_id=' . $class_id . '" class="' . $cssClass . '">' . $class_name . '</a>';
          }
          ?>
        </div>
        <div class="content-wrapper">
          <div class="col">
            <h2>Select Class you want to Download Data.</h2>
          </div>
        </div>
      </div>
    </div>

    <script>
      const printBtn = document.getElementById('print');

      function preparePrintContent() {
        const content = document.createElement('div');
        content.innerHTML = '<html><head><title>Print</title></head><body>';
        content.innerHTML += document.getElementById('print-content').innerHTML;
        content.innerHTML += '</body></html>';
        return content;
      }

      printBtn.addEventListener('click', function () {
        // Prepare the content to be printed
        const printContent = preparePrintContent();

        // Create a new window
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent.innerHTML);

        // Close the document and trigger printing
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
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