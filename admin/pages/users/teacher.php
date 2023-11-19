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
  <link rel="stylesheet" href="../../css/styles.css">
  <link rel="stylesheet" href="assets/css/notification.css">
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
                <a href="../feedback/feedback.php" class="dropdown-item preview-item">
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
                  <a href="../announcement/announcement.php" class="dropdown-item preview-item">
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
                <li class="nav-item"><a class="nav-link" href="teacher.php">Teacher</a></li>
                <li class="nav-item"><a class="nav-link" href="student.php">Student</a></li>
                <li class="nav-item"><a class="nav-link" href="parent.php">Parent</a></li>
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
                <li class="nav-item"> <a class="nav-link" href="../reports/student_report.php">Student Reports</a></li>
                <li class="nav-item"> <a class="nav-link" href="../reports/teacher_report.php">Teacher Reports</a></li>
                <li class="nav-item"> <a class="nav-link" href="../reports/parent_report.php">Parent Reports</a></li>
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
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 grid-margin stretch-card" style="margin-left: -20px;">
              <div class="card">
                <div class="row">
                  <div class="col-md-6">
                    <div class="card-body">
                      <h1 class="card-title" style="font-size: 30px; margin-left: 10px;">Teacher List</h1>
                      <a href="create_teacher.php" class="btn btn-success" style="margin-left: 10px;">Add New
                        Teacher</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col mb-3">
              <?php
              if (isset($_GET['msg'])) {
                $msg = $_GET['msg'];
                echo '<div class="alert alert-success alert-dismissible fade show w-75" role="alert">
                            ' . $msg . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            </div>';
              }
              ?>
            </div>
            <div class="col-12 grid-margin stretch-card" style="margin-left: -20px;">
              <div class="card">
                <div class="row">
                  <div class="col-md-12">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="example" class="table text-center">
                          <thead class="table" style="background-color: #4BB543; color: white;">
                            <tr>
                              <th scope="col" style="text-align: center; overflow: hidden;">First Name</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Middle Name</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Last Name</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">House Address</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Email</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Contact Number</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Department</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">User Type</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            include("db_conn.php");

                            $sql = "SELECT * FROM user_account WHERE usertype = 'teacher'";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                              ?>
                              <tr>
                                <td>
                                  <?php echo $row['firstname'] ?>
                                </td>
                                <td>
                                  <?php echo $row['middlename'] ?>
                                </td>
                                <td>
                                  <?php echo $row['lastname'] ?>
                                </td>
                                <td>
                                  <?php echo $row['address'] ?>
                                </td>
                                <td >
                                  <?php echo $row['email'] ?>
                                </td>
                                <td>
                                  <?php echo $row['contact'] ?>
                                </td>
                                <td>
                                  <?php echo $row['department'] ?>
                                </td>
                                <td>
                                  <?php echo $row['usertype'] ?>
                                </td>
                                <td style="font-size: 18px; overflow: hidden;">
                                  <a href="edit_teacher.php?updateid=<?php echo $row['user_id'] ?>" class="link-dark"
                                    style="margin-right: 5px;"><i class="bi bi-pencil-square"></i></a>
                                  <a href="delete_teacher.php?deleteid=<?php echo $row['user_id'] ?>" class="link-dark"><i
                                      class="bi bi-trash-fill"></i></a>
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