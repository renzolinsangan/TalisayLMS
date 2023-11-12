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

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 grid-margin stretch-card" style="margin-left: -20px;">
              <div class="card">
                <div class="row">
                  <div class="col-md-6">
                    <div class="card-body">
                      <h1 class="card-title" style="font-size: 30px; margin-left: 10px;">Announcement</h1>
                      <a href="create_announcement.php" class="btn btn-success" style="margin-left: 10px;">Create new
                        Announcement</a>
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
                        <table id="example" class="table text-center"
                        style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                          <thead class="table" style="background-color: #4BB543; color: white;">
                            <tr>
                              <th scope="col" style="text-align: center; overflow: hidden;">Title</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Type</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Name</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Date</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Division/Track</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Start Date</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">End Date</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Detail</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Attachment</th>
                              <th scope="col" style="text-align: center; overflow: hidden;">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            include("db_conn.php");

                            $sql = "SELECT * FROM news";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                              ?>
                              <tr>
                                <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                  <?php echo $row['title'] ?>
                                </td>
                                <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                  <?php echo $row['type'] ?>
                                </td>
                                <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                  <?php echo $row['name'] ?>
                                </td>
                                <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                  <?php echo $row['date'] ?>
                                </td>
                                <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                  <?php echo $row['track'] ?>
                                </td>
                                <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                  <?php echo $row['start_date'] ?>
                                </td>
                                <td style="padding: 3vh !important; font-size: 14px; overflow: hidden;">
                                  <?php echo $row['end_date'] ?>
                                </td>
                                <td style="padding: 3vh !imporant; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                  <?php echo $row['detail'] ?>
                                </td>
                                <td>
                                  <?php
                                  $attachment = $row['attachment'];

                                  // Check the file extension to determine the type
                                  $fileExtension = pathinfo($attachment, PATHINFO_EXTENSION);

                                  if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                                    // Display image
                                    echo '<img src="assets/image/announcement_upload/' . $attachment . '" width="100" height="100" style="border-radius: 10px;">';
                                  } elseif (in_array($fileExtension, ['pdf', 'docx', 'ppt'])) {
                                    // Display file icon or link
                                    echo '<a href="assets/image/' . $attachment . '" target="_blank"><i class="bi bi-file-earmark"></i></a>';
                                  } else {
                                    // Unknown file type, display a default icon or message
                                    echo '<i class="bi bi-question-square"></i>';
                                  }
                                  ?>
                                </td>

                                <td style="font-size: 18px;">
                                  <a href="edit_announcement.php?updateid=<?php echo $row['news_id'] ?>" class="link-dark"
                                    style="margin-right: 5px;"><i class="bi bi-pencil-square"></i></a>
                                  <a href="delete_announcement.php?deleteid=<?php echo $row['news_id'] ?>"
                                    class="link-dark"><i class="bi bi-trash-fill"></i></a>
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