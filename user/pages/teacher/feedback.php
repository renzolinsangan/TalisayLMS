<?php
session_start();
$teacher_id = $_SESSION['user_id'];
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

include_once("config.php");

$firstname = "";
$lastname = "";

// Check if the user is logged in and retrieve their information
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // Replace 'your_table_name' with the actual name of the table where user information is stored
  $sql = "SELECT firstname, lastname FROM user_account WHERE user_id = ?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$user_id]);

  // Fetch the user's information
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Assign firstname and lastname values
  if ($user) {
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
  }
}

if (isset($_POST['submit'])) {
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $report_title = $_POST['report_title'];
  $details = $_POST['details'];
  $attachment = $_POST['attachment'];

  $sql = "INSERT INTO feedback (firstname, lastname, report_title, details, attachment) VALUES (?, ?, ?, ?, ?)";
  $stmtinsert = $db->prepare($sql);
  $result = $stmtinsert->execute([$firstname, $lastname, $report_title, $details, $attachment]);

  if ($result) {
    header("Location: feedback.php?msg=Please Continue on giving feedbacks for improvements, Thank You!");
  } else {
    echo "Failed: " . mysqli_error($conn);
  }
}

$user_id = $_SESSION['user_id'];

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
  <link rel="stylesheet" href="assets/css/feedback.css">
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
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h2>Feedback</h2>
                  <p class="card-description" style="margin-bottom: 2vh;">
                    Send a feedback for a better improvements.
                  </p>
                  <form action="" method="post" class="forms-sample" id="myForm">
                    <div class="col-mb-4">
                      <div id="validationAlert" class="alert alert-danger alert-dismissible fade show" role="alert"
                        style="display: none;">
                        Please fill in all required fields.
                      </div>
                    </div>
                    <?php
                    if (isset($_GET['msg'])) {
                      $msg = $_GET['msg'];
                      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            ' . $msg . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>';
                    }
                    ?>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label" style="font-size: 20px;">First Name</label>
                          <div class="col-sm-9">
                            <input type="text" name="firstname" class="form-control" value="<?php echo $firstname; ?>"
                              readonly />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label" style="font-size: 20px;">Last Name</label>
                          <div class="col-sm-9">
                            <input type="text" name="lastname" class="form-control" value="<?php echo $lastname; ?>"
                              readonly />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1" style="font-size: 20px;">Report Title</label>
                      <input type="text" name="report_title" class="form-control" id="exampleInputTitle1">
                    </div>
                    <div class="form-group">
                      <label for="exampleTextarea1" style="font-size: 20px;">Details</label>
                      <textarea class="form-control" name="details" id="exampleTextarea1" rows="12"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 10vh;">
                      <label>File upload</label>
                      <input type="file" name="attachment" class="file-upload-default">
                      <div class="input-group col-xs-12">
                        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                        <span class="input-group-append">
                          <button class="file-upload-browse btn btn-success" type="button">Upload</button>
                        </span>
                      </div>
                    </div>
                    <a href="index.php">
                      <span class="btn btn-danger" style="margin-right: 2vh;">Cancel</span>
                    </a>
                    <button type="submit" name="submit" class="btn btn-success mr-2">Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- content-wrapper ends -->
  </div>
  <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
  </div>

  <script>
    var form = document.getElementById('myForm');
    var validationAlert = document.getElementById('validationAlert');

    form.addEventListener('submit', function (event) {
      var firstnameInput = form.querySelector('input[name="firstname"]');
      var lastnameInput = form.querySelector('input[name="lastname"]');
      var titleInput = form.querySelector('input[name="report_title"]');
      var detailsTextArea = form.querySelector('textarea[name="details"]');

      var isInvalid = false;

      // Check if firstname and lastname contain only letters or special characters
      if (firstnameInput.value === '') {
        firstnameInput.classList.add('is-invalid');
        isInvalid = true;
      } else {
        firstnameInput.classList.remove('is-invalid');
      }

      if (lastnameInput.value === '') {
        lastnameInput.classList.add('is-invalid');
        isInvalid = true;
      } else {
        lastnameInput.classList.remove('is-invalid');
      }

      var namePattern = /^[A-Za-z\s]+$/;

      if (!namePattern.test(firstnameInput.value)) {
        firstnameInput.classList.add('is-invalid');
        isInvalid = true;
      } else {
        firstnameInput.classList.remove('is-invalid');
      }

      if (!namePattern.test(lastnameInput.value)) {
        lastnameInput.classList.add('is-invalid');
        isInvalid = true;
      } else {
        lastnameInput.classList.remove('is-invalid');
      }

      if (titleInput.value === '') {
        titleInput.classList.add('is-invalid');
        isInvalid = true;
      } else {
        titleInput.classList.remove('is-invalid');
      }

      if (detailsTextArea.value === '') {
        detailsTextArea.classList.add('is-invalid');
        isInvalid = true;
      } else {
        detailsTextArea.classList.remove('is-invalid');
      }

      // Check if report title contains only letters with numbers and special characters
      var titlePattern = /^[A-Za-z0-9\s!@#$%^&*()_+|~=`{}\[\]:;"'<>,?/]+$/;
      if (!titlePattern.test(titleInput.value)) {
        titleInput.classList.add('is-invalid');
        isInvalid = true;
      } else {
        titleInput.classList.remove('is-invalid');
      }

      if (isInvalid) {
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
  <script src="../../js/file-upload.js"></script>
  <script src="../../js/typeahead.js"></script>
  <script src="../../js/select2.js"></script>
  <!-- endinject -->
</body>

</html>