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
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/student_report.css">
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
                <li class="nav-item"> <a class="nav-link"
                    href="grade_report.php?user_id=<?php echo $teacher_id ?>">Report of Grades</a>
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
      <!-- partial -->
      <div class="main-panel">
        <div class="header-links" style="overflow-x: auto; white-space: nowrap;">
          <?php
          include("config.php");

          $classFromUrl = isset($_GET['class_name']) ? urldecode($_GET['class_name']) : '';

          $sql = "SELECT DISTINCT class_name, tc_id FROM class_enrolled WHERE teacher_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          $class_names_and_tc_ids = array();

          while ($row = $result->fetch_assoc()) {
            $class_names_and_tc_ids[] = $row;
          }

          foreach ($class_names_and_tc_ids as $class_data) {
            $class_name = $class_data['class_name'];
            $tc_id = $class_data['tc_id'];

            $safeClass = str_replace(' ', '_', $class_name);
            $cssClass = ($classFromUrl === $class_name) ? 'active' : '';

            $encodedClass = urlencode($class_name);

            echo '<a href="grade_report_subject.php?user_id=' . $teacher_id . '&class_name=' . $encodedClass . '&class_id=' . $tc_id . '" class="' . $cssClass . '">' . $class_name . '</a>';
          }
          ?>
        </div>
        <div class="content-wrapper">
          <button id="print" class="btn btn-success mb-2">Download Data</button>
          <div id="print-content">
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card-body">
                        <h1 class="card-title" style="font-size: 30px; margin-left: 10px;">Grade
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
                            <table class="table table-bordered table-hover text-center">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <th scope="col">Student Name</th>
                                <?php
                                include("config.php");
                                $class_id = $_GET['class_id'];

                                $sqlQuestion = "SELECT title, date, point, 'Question' as type FROM classwork_question 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuestion = $db->prepare($sqlQuestion);
                                $stmtQuestion->execute([$class_id, $teacher_id]);
                                $questionTitles = $stmtQuestion->fetchAll(PDO::FETCH_ASSOC);

                                $sqlAssignment = "SELECT title, date, point, 'Assignment' as type FROM classwork_assignment 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtAssignment = $db->prepare($sqlAssignment);
                                $stmtAssignment->execute([$class_id, $teacher_id]);
                                $assignmentTitles = $stmtAssignment->fetchAll(PDO::FETCH_ASSOC);

                                $sqlQuiz = "SELECT quizTitle as title, date, quizPoint as point, 'Quiz' as type FROM classwork_quiz 
                                WHERE class_id = ? AND teacher_id = ?";
                                $stmtQuiz = $db->prepare($sqlQuiz);
                                $stmtQuiz->execute([$class_id, $teacher_id]);
                                $quizTitles = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

                                $sqlExam = "SELECT examTitle as title, date, examPoint as point, 'Exam' as type FROM classwork_exam 
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
                                    <th scope="col" style="text-align: left;">
                                        <p style="color: white; margin-bottom: -3px;">
                                            <?php echo $formattedDate; ?>
                                        </p>
                                        <p style="border-bottom: 1px solid white; color: black;">
                                            <?php echo $title['type'] . ': ' . $title['title']; ?>
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
                                    <td>
                                      <?php echo $student['student_firstname'] . ' ' . $student['student_lastname'] ?>
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
        const printContent = preparePrintContent();

        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent.innerHTML);

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