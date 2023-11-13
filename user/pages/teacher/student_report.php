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

$sql = "SELECT DISTINCT class_name FROM class_enrolled WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$class_names = array();

while ($row = $result->fetch_assoc()) {
  $class_names[] = $row['class_name'];
}

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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="assets/css/report_student.css">
  <!-- endinject -->
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
        <div class="card d-flex align-items-left justify-content-between"
          style="position: relative; background-color: none; height: 8vh;">
          <div class="row">
            <div class="col-1" style="margin-left: 35px; margin-top: 9px;">
              <a href="student_report.php?user_id=<?= $teacher_id ?>" class="nav-link active"
              style="color: green; text-decoration: none; font-size: 14px;">All</a>
            </div>
            <div class="row">
              <?php
              $sqlSection = "SELECT DISTINCT section FROM section WHERE teacher_id = ?";
              $stmtSection = $conn->prepare($sqlSection);
              $stmtSection->bind_param("i", $user_id);
              $stmtSection->execute();
              $sectionResult = $stmtSection->get_result();

              while ($sectionrow = $sectionResult->fetch_assoc()):
                $section = $sectionrow['section'];
                ?>
                <div class="col">
                  <div class="dropdown-container">
                    <div class="dropdown" style="position: relative;">
                      <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"
                        style="background-color: transparent; color: green; margin-top: 10px;">
                        <?= $section ?>
                      </button>
                      <div class="dropdown-menu">
                        <?php
                        $sqlSubjects = "SELECT class_id, class_name, subject FROM section WHERE teacher_id = ? AND section = ?";
                        $stmtSubjects = $conn->prepare($sqlSubjects);
                        $stmtSubjects->bind_param("is", $user_id, $section);
                        $stmtSubjects->execute();
                        $subjectsResult = $stmtSubjects->get_result();

                        while ($subjectRow = $subjectsResult->fetch_assoc()):
                          $class_id = $subjectRow['class_id'];
                          $class_name = $subjectRow['class_name'];
                          $subject = $subjectRow['subject'];
                          ?>
                          <a class="dropdown-item" href="student_report_subject.php?user_id=<?php echo $user_id ?>&class_id=<?php echo $class_id ?>&class_name=<?php echo $class_name ?>" 
                          style="background-color: transparent;">
                            <?= $subject ?>
                          </a>
                        <?php endwhile; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>
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
                        <h1 class="card-title" style="font-size: 30px; margin-left: 10px; margin-bottom: -20px;">
                          Reports
                          in All Handled
                          Students
                        </h1>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card-body">
                          <div class="table-responsive">
                            <table id="example" class="table table-hover text-center"
                              style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <tr>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Student's Name</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Class Name</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Section</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Subject</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Grade Level</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Department</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                include("db_conn.php");
                                $sql = "SELECT * FROM class_enrolled WHERE teacher_id=$user_id";
                                $result = mysqli_query($conn, $sql);

                                while ($row = mysqli_fetch_assoc($result)) {
                                  ?>
                                  <tr>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['student_firstname'] . ' ' . $row['student_lastname']; ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['class_name'] ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['section'] ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['subject'] ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['grade_level'] ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['strand'] ?>
                                    </td>
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
    <script src="../../js/off-canvas.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/settings.js"></script>
    <script src="../../js/todolist.js"></script>
</body>

</html>