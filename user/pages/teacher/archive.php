<?php
function generateClassCode($length = 7)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $classCode = '';
  for ($i = 0; $i < $length; $i++) {
    $classCode .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $classCode;
}

session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

include("config.php");
$teacher_id = $_SESSION['user_id'];

$sql_fetch_teacher = "SELECT firstname, lastname, department FROM user_account WHERE user_id = ?";
$stmt_fetch_teacher = $db->prepare($sql_fetch_teacher);
$stmt_fetch_teacher->execute([$teacher_id]);
$teacher_data = $stmt_fetch_teacher->fetch(PDO::FETCH_ASSOC);
$first_name = $teacher_data['firstname'];
$last_name = $teacher_data['lastname'];
$department = $teacher_data['department'];

if (isset($_POST['submit'])) {
  $class_name = $_POST['class_name'];
  $section = $_POST['section'];
  $subject = $_POST['subject'];
  $strand = $_POST['strand'];
  $class_code = generateClassCode();

  $sql = "INSERT INTO section (class_name, section, subject, strand, teacher_id, class_code, first_name, last_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $stmtinsert = $db->prepare($sql);
  $result = $stmtinsert->execute([$class_name, $section, $subject, $strand, $teacher_id, $class_code, $first_name, $last_name]);

  if ($result) {
    header("Location: course.php?msg=Class created successfully!");
    exit();
  } else {
    echo "Error: ";
  }
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT profile FROM user_profile WHERE user_id = :user_id AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_COLUMN);
$stmt->closeCursor();

include("config.php");
if (isset($_POST['unarchive'])) {
  $class_id = $_POST['class_id'];

  $sql_update_section = "UPDATE section SET archive_status = '' WHERE class_id = ?";
  $stmt_update_section = $db->prepare($sql_update_section);
  $stmt_update_section->execute([$class_id]);

  if ($stmt_update_section->rowCount() > 0) {
    $sql_update_classEnrolled = "UPDATE class_enrolled SET archive_status = '' WHERE tc_id = ?";
    $stmt_update_classEnrolled = $db->prepare($sql_update_classEnrolled);
    $stmt_update_classEnrolled->execute([$class_id]);

    header("Location: course.php?");
    exit();
  } else {
    header("Location: error.php");
    exit();
  }
}
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
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/course.css">
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
            <div class="col-md-4 mb-2">
                <h2>
                    Archive Courses
                </h2>
                <p class="text-body-secondary">
                    (Teacher)
                </p>
            </div>
          </div>
          <div class="row">
            <?php
            include("db_conn.php");
            $teacher_id = $_SESSION['user_id'];

            $sql = "SELECT * FROM section WHERE teacher_id = ? AND strand = ? AND archive_status = 'archive'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $teacher_id, $department);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
              $_SESSION['class_name'] = $row['class_name'];
              $_SESSION['section'] = $row['section'];
              $firstName = ucfirst(strtolower($_SESSION['first_name']));
              $lastName = ucfirst(strtolower($_SESSION['last_name']));
              $_SESSION['teacher_name'] = $firstName . " " . $lastName;

              $class_id = $row['class_id']; // Assuming you have a class_id column
              $sql = "SELECT theme FROM class_theme WHERE teacher_id = :teacher_id AND class_id = :class_id AND theme_status = 'recent'";
              $stmt = $db->prepare($sql);
              $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
              $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
              $stmt->execute();
              $themeData = $stmt->fetch(PDO::FETCH_ASSOC);
              $stmt->closeCursor();

              if ($themeData) {
                $theme = $themeData['theme'];
              } else {
                $theme = 'background-color: green';
              }
              ?>
              <div class="col-md-4 grid-margin transparent">
                <div class="card card-tale text-center"
                  style="height: 50vh; flex-direction: column; justify-content: space-between;">
                  <a href="class_course.php?class_id=<?php echo $row['class_id']; ?>&class_name=<?php echo $row['class_name'] ?>"
                    class="course">
                    <div class="card-header"
                      style="text-align: left; background-image: url(assets/image/<?php echo $theme ?>); background-color: green; background-size: cover;">
                      <div class="course-top">
                        <p class="course-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                          <?php echo $row['class_name'] ?>
                        </p>
                        <p class="course-section" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                          <?php echo $row['section'] ?>
                        </p>
                      </div>
                      <?php
                      $firstName = ucfirst(strtolower($_SESSION['first_name']));
                      $lastName = ucfirst(strtolower($_SESSION['last_name']));
                      $_SESSION['teacher_name'] = $firstName . " " . $lastName;
                      echo "<p class='course-teacher'>" . $firstName . " " . $lastName . "</p>";
                      ?>

                      <div class="circle-image" id="circle-image">
                        <img src="assets/image/<?php echo $profile ?>" alt="profile"
                          onerror="this.src='images/profile.png'">
                      </div>
                    </div>
                  </a>
                  <form action="" method="post">
                    <div class="card-footer d-flex justify-content-end">
                      <button class="unenroll" id="unenroll" type="button" data-bs-toggle="modal"
                        data-bs-target="#staticBackdrop<?php echo $row['class_id']; ?>">
                        <h5>Unarchive <i class="bi bi-archive" style="font-size: 20px;"></i></i></h5>
                      </button>
                      <div class="modal fade" id="staticBackdrop<?php echo $row['class_id']; ?>" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog" style="width: 50vh; margin-top: 25vh;">
                          <div class="modal-content">
                            <div class="modal-body">
                              <div class="text-start">
                                <h3>Unarchive
                                  <?php echo $row['class_name'] ?>?
                                </h3>
                                <p class="text-body-secondary mt-3">Class will be moved to the courses.</p>
                                <p class="text-body-secondary mt-3">Do you want to unarchive this class?</p>
                                <p class="text-body-secondary mt-3">Press click unarchive button if yes.</p>
                              </div>
                              <input type="hidden" name="class_id" value="<?php echo $row['class_id']; ?>">

                              <div class="modal-button mt-3 d-flex justify-content-end align-items-end">
                                <button type="button" class="btn" data-bs-dismiss="modal"
                                  style="margin-right: 2vh; padding: 0;">Cancel</button>
                                <button type="submit" class="btn"
                                  name="unarchive"
                                  style="color: green; margin-top: 2vh; padding: 0;">Unarchive</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <?php
            }
            ?>
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

      if (classnameInput.value.trim() === '') {
        isEmpty = true;
        classnameInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
      } else {
        classnameInput.classList.remove('is-invalid'); // Remove the class if it's valid
      }

      if (sectionInput.value.trim() === '') {
        isEmpty = true;
        sectionInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
      } else {
        sectionInput.classList.remove('is-invalid'); // Remove the class if it's valid
      }

      if (subjectInput.value.trim() === '') {
        isEmpty = true;
        subjectInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
      } else {
        subjectInput.classList.remove('is-invalid'); // Remove the class if it's valid
      }

      if (strandDropdown.value === '') {
        isEmpty = true;
        strandDropdown.classList.add('is-invalid'); // Add a class to highlight the invalid input
      } else {
        strandDropdown.classList.remove('is-invalid'); // Remove the class if it's valid
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