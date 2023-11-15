<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location:../../admin_login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS Admin</title>
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="assets/css/student_report.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
</head>

<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="../../index.php"><img src="../../images/trace.svg" class="mr-2"
            alt="logo" />Talisay LMS</a>
        <a class="navbar-brand brand-logo-mini" href="../../index.php"><img src="../../images/trace.svg"
            alt="logo" /></a>
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

            // Fetch feedback notifications
            $sqlFeedbackNotif = "SELECT firstname, lastname, date FROM feedback ORDER BY date DESC";
            $resultFeedbackNotif = $db->query($sqlFeedbackNotif);

            // Fetch news items
            $sqlNews = "SELECT type, title, end_date FROM news";
            $resultNews = $db->query($sqlNews);

            // Current date
            $currentDate = date('Y-m-d');
            ?>

            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
              aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>

              <?php
              while ($row = $resultFeedbackNotif->fetch(PDO::FETCH_ASSOC)) {
                $fullName = $row['firstname'] . ' ' . $row['lastname'];
                $submissionDate = $row['date'];
                ?>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                      <i class="ti-info-alt mx-0"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject font-weight-normal">
                      <?php echo $fullName; ?> has sent a feedback
                    </h6>
                    <p class="font-weight-light small-text mb-0 text-muted">
                      <?php echo date('F j', strtotime($submissionDate)); ?>
                    </p>
                  </div>
                </a>
              <?php } ?>

              <?php
              while ($newsRow = $resultNews->fetch(PDO::FETCH_ASSOC)) {
                $type = $newsRow['type'];
                $title = $newsRow['title'];
                $endDate = $newsRow['end_date'];

                if ($currentDate > $endDate) {
                  ?>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-danger">
                        <i class="ti-alarm-clock mx-0"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <h6 class="preview-subject font-weight-normal">
                        <?php echo ucfirst($type); ?> -
                        <?php echo $title; ?> has expired
                      </h6>
                      <p class="font-weight-light small-text mb-0 text-muted">
                        <?php echo date('F j', strtotime($endDate)); ?>
                      </p>
                    </div>
                  </a>
                <?php }
              } ?>
            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="../../images/faces/profile.png" alt="profile" />
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a href="../../admin_logout.php" class="dropdown-item">
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
            <a class="nav-link" href="../../index.php">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" href="../announcement/announcement.php">
              <i class="menu-icon"><i class="bi bi-megaphone"></i></i>
              <span class="menu-title">Announcement</span>
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
                <li class="nav-item"><a class="nav-link" href="../users/teacher.php">Teacher</a></li>
                <li class="nav-item"><a class="nav-link" href="../users/student.php">Student</a></li>
                <li class="nav-item"><a class="nav-link" href="../users/parent.php">Parent</a></li>
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
                <li class="nav-item"> <a class="nav-link" href="student_report.php">Student Reports</a></li>
                <li class="nav-item"> <a class="nav-link" href="teacher_report.php">Teacher Reports</a></li>
                <li class="nav-item"> <a class="nav-link" href="parent_report.php">Parent Reports</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item mb-3">
            <a class="nav-link" href="../feedback/feedback.php">
              <i class="menu-icon"><i class="bi bi-chat-right-quote"></i></i>
              <span class="menu-title">Feedbacks</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="main-panel">
        <div class="header-links" style="overflow-x: auto; white-space: nowrap;">
          <a href="student_report.php" class="humss" style="margin-left: 5vh;">All</a>
          <a href="student_reportstem.php" class="stem">STEM</a>
          <a href="student_reporthumss.php" class="nav-link active">HUMSS</a>
          <a href="student_reportabm.php" class="abm">ABM</a>
          <a href="student_reporttvl.php" class="mechanic">TVL</a>
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
                        <h1 class="card-title" style="font-size: 30px; margin-left: 10px; margin-bottom: -20px;">HUMSS
                          students</h1>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card-body">
                          <div class="table-responsive">
                            <table id="example" class="table text-center"
                              style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                              <thead class="table" style="background-color: #4BB543; color: white;">
                                <tr>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Student's Name</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">House Address</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Contact Number</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Email Address</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Grade Level</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Department</th>
                                  <th scope="col" style="text-align: center; overflow: hidden;">Section</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                include("db_conn.php");
                                $sql = "SELECT * FROM user_account WHERE usertype='student' AND department='humss'";
                                $result = mysqli_query($conn, $sql);

                                while ($row = mysqli_fetch_assoc($result)) {
                                  ?>
                                  <tr>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['firstname'] . ' ' . ucfirst(substr($row['middlename'], 0, 1)) . '. ' . $row['lastname']; ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['address']; ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['contact']; ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['email']; ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['grade_level'] ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo strtoupper($row['department']) ?>
                                    </td>
                                    <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                      <?php echo $row['section'] ?>
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
    <!-- Include DataTables JavaScript and jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Initialize DataTables after including the necessary files -->
    <script>
      $(document).ready(function () {
        $('#example').DataTable();
      });
    </script>
    <script>
      const printBtn = document.getElementById('print');

      // Function to prepare the content to be printed
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
    <script src="../../js/off-canvas.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/settings.js"></script>
    <script src="../../js/todolist.js"></script>
</body>

</html>