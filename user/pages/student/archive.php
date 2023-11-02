<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$student_id = $_SESSION['user_id'];

$sql_fetch_student = "SELECT firstname, lastname, section, grade_level FROM user_account WHERE user_id = ?";
$stmt_fetch_student = $db->prepare($sql_fetch_student);
$stmt_fetch_student->execute([$student_id]);
$student_data = $stmt_fetch_student->fetch(PDO::FETCH_ASSOC);
$student_firstname = $student_data['firstname'];
$student_lastname = $student_data['lastname'];
$student_section = $student_data['section'];
$student_gradelevel = $student_data['grade_level'];

if (isset($_POST['submit_code'])) {
  $class_code = $_POST['class_code'];
  $_SESSION['class_code'] = $class_code;

  $sql = "SELECT * FROM section WHERE class_code=?";
  $stmt = $db->prepare($sql);
  $stmt->execute([$class_code]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    // Check if section and grade_level match user's information
    if ($row['section'] === $student_section) {
      $_SESSION['class_id'] = $row['class_id'];
      $_SESSION['class_name'] = $row['class_name'];
      $_SESSION['section'] = $row['section'];
      $_SESSION['subject'] = $row['subject'];
      $_SESSION['strand'] = $row['strand'];
      $_SESSION['teacher_id'] = $row['teacher_id'];
      $_SESSION['first_name'] = $row['first_name'];
      $_SESSION['last_name'] = $row['last_name'];

      $sql_insert_enrollment = "INSERT INTO class_enrolled (tc_id, class_name, section, subject, grade_level, strand, teacher_id, class_code, first_name, last_name, student_id, student_firstname, student_lastname) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt_insert_enrollment = $db->prepare($sql_insert_enrollment);
      $result_insert = $stmt_insert_enrollment->execute([
        $_SESSION['class_id'],
        $_SESSION['class_name'],
        $_SESSION['section'],
        $_SESSION['subject'],
        $student_gradelevel,
        $_SESSION['strand'],
        $_SESSION['teacher_id'],
        $_SESSION['class_code'],
        $_SESSION['first_name'],
        $_SESSION['last_name'],
        $student_id,
        $student_firstname,
        $student_lastname
      ]);

      if ($result_insert) {
        header("Location: course.php");
        exit;
      } else {
        echo "Error: Unable to enroll in class.";
      }
    } else {
      // Section or grade_level doesn't match, redirect with an error message
      header("Location: course.php?msg=You are not from that class, please try again!");
      exit;
    }
  } else {
    // Class code not found, redirect with an error message
    header("Location: course.php?msg=Invalid class code, please try again!");
    exit;
  }
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
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-4 mb-2">
              <h2>
                Archive Courses
              </h2>
              <p class="text-body-secondary">
                (Student)
              </p>
            </div>
          </div>
          <div class="row">
            <?php
            if (isset($_GET['msg'])) {
              $msg = $_GET['msg'];
              echo '<div class="alert alert-danger alert-dismissible fade show w-50" role="alert">
                ' . $msg . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                </div>';
            }
            ?>
          </div>
          <div class="row">
            <?php
            include("db_conn.php");
            $class_code = isset($_SESSION['class_code']) ? $_SESSION['class_code'] : '';

            $sql_enrolled = "SELECT ce.class_id, se.class_name, se.section, se.first_name, se.last_name, se.teacher_id
                FROM class_enrolled ce
                INNER JOIN section se ON ce.class_code = se.class_code
                WHERE ce.student_id = ? AND ce.archive_status = 'archive' AND se.archive_status = 'archive'";
            $stmt_enrolled = $conn->prepare($sql_enrolled);
            $stmt_enrolled->bind_param("i", $student_id);
            $stmt_enrolled->execute();
            $result = $stmt_enrolled->get_result();
            ?>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
              $_SESSION['class_id'] = $row['class_id'];
              $_SESSION['class_name'] = $row['class_name'];
              $_SESSION['section'] = $row['section'];
              $_SESSION['first_name'] = $row['first_name'];
              $_SESSION['last_name'] = $row['last_name'];
              $_SESSION['teacher_id'] = $row['teacher_id'];

              $sql_teacher_profile = "SELECT profile FROM user_profile WHERE user_id = :teacher_id AND profile_status = 'recent'";
              $stmt_teacher_profile = $db->prepare($sql_teacher_profile);
              $stmt_teacher_profile->bindParam(':teacher_id', $_SESSION['teacher_id'], PDO::PARAM_INT);
              $stmt_teacher_profile->execute();
              $teacher_profile_data = $stmt_teacher_profile->fetch(PDO::FETCH_ASSOC);
              $stmt_teacher_profile->closeCursor();

              if ($teacher_profile_data) {
                $teacher_profile = $teacher_profile_data['profile'];
              }

              $sql = "SELECT theme FROM class_theme WHERE teacher_id = :teacher_id AND class_name = :class_name AND theme_status = 'recent'";
              $stmt = $db->prepare($sql);
              $stmt->bindParam(':teacher_id', $_SESSION['teacher_id'], PDO::PARAM_INT);
              $stmt->bindParam(':class_name', $_SESSION['class_name'], PDO::PARAM_STR);
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
                  <a href="archive_classCourse.php?class_id=<?php echo $row['class_id']; ?>" class="course">
                    <div class="card-header"
                      style="text-align: left; background-image: url(../teacher/assets/image/<?php echo $theme ?>); background-color: green; background-size: cover;">
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
                        <img src="../teacher/assets/image/<?php echo $teacher_profile ?>" alt="profile"
                          onerror="this.src='images/profile.png'">
                      </div>
                    </div>
                  </a>
                  <div class="card-footer d-flex justify-content-end">
                    <button class="unenroll" id="unenroll" type="button" data-bs-toggle="modal"
                      data-bs-target="#staticBackdrop<?php echo $row['class_id']; ?>">
                      <h5>Unenroll <i class="bi bi-journal-x" style="font-size: 20px;"></i></h5>
                    </button> 
                    <div class="modal fade" id="staticBackdrop<?php echo $row['class_id']; ?>" data-bs-backdrop="static"
                      data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog" style="width: 50vh; margin-top: 25vh;">
                        <div class="modal-content">
                          <div class="modal-body">
                            <div class="text-start">
                              <h3>Unenroll from
                                <?php echo $row['class_name'] ?>?
                              </h3>
                              <p class="text-body-secondary mt-3">You will be removed from this class.</p>
                              <p class="text-body-secondary mt-3">Do you want to leave from this class?</p>
                              <p class="text-body-secondary mt-3">Press unenroll button if yes.</p>
                            </div>

                            <div class="modal-button mt-3 d-flex justify-content-end align-items-end">
                              <button type="button" class="btn" data-bs-dismiss="modal"
                                style="margin-right: 2vh; padding: 0;">Cancel</button>
                              <a href="delete_course.php?deleteid=<?php echo $row['class_id'] ?>">
                                <button type="button" class="btn"
                                  style="color: green; margin-top: 2vh; padding: 0;">Unenroll</button>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
        </div>
      </div>
    </div>
    
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