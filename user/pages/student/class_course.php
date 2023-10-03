<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
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
}

$sql = "SELECT profile FROM user_profile WHERE user_id = :user_id AND profile_status = 'recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_COLUMN);
$stmt->closeCursor();

$sql = "SELECT theme FROM class_theme WHERE class_name=:class_name AND teacher_id=:teacher_id AND theme_status='recent'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':class_name', $class_name, PDO::PARAM_STR);
$stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
$stmt->execute();
$theme = $stmt->fetch(PDO::FETCH_COLUMN);
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
  <link rel="stylesheet" href="assets/css/class_course.css">
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
        <div class="header-sticky">
          <div class="header-links">
            <?php
            if (isset($_GET['class_id'])) {
              $class_id = $_GET['class_id'];
              ?>
              <a class="btn-success" href="course.php"><i class="bi bi-arrow-bar-left" style="color: white;"></i></a>
              <a href="class_course.php?class_id=<?php echo $class_id ?>" class="nav-link active"
                style="margin-left: 2vh;">Stream</a>
              <a href="class_classwork.php?class_id=<?php echo $class_id ?>" class="people">Classwork</a>
              <a href="class_people.php?class_id=<?php echo $class_id ?>" class="people">People</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 grid-margin stretch-card" style="margin-top: 10vh;">
              <div class="card">
                <div class="row">
                  <div class="col">
                    <div class="card-body" style="height: 45vh; display: flex; flex-direction: column; justify-content: flex-end;
                          <?php
                          if ($theme !== "../teacher/assets/image/") {
                            echo "background-image: url(../teacher/assets/image/$theme);";
                            echo "color: white;";
                          } else {
                            echo "background-color: green;";
                          }
                          ?>
                          background-color: green; background-size: cover; background-position: cover;">
                      <?php
                      include("db_conn.php");

                      if (isset($_GET['class_id'])) {
                        $class_id = $_GET['class_id'];

                        // Retrieve class details based on class_id
                        $sql = "SELECT * FROM class_enrolled WHERE class_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $class_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $class_data = $result->fetch_assoc();

                        if ($class_data) {
                          $class_name = $class_data['class_name'];
                          $section = $class_data['section'];
                          $class_code = $class_data['class_code'];
                          $_SESSION['first_name'] = $class_data['first_name'];
                          $_SESSION['last_name'] = $class_data['last_name'];

                          echo "<h2>$class_name</h2>";
                          echo "<h3>$section</h3>";
                        }
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="content-wrapper" style="margin-top: -50px;">
              <div class="row">
                <div class="col-md-3 mb-3">
                  <div class="card"
                    style="background-color: white; border-radius: 5; padding: 20px; border: 1px solid rgba(128, 128, 128, 0.5);">
                    <h4>Upcoming</h4>
                    <p class="text-body-secondary mt-3">Check the works assigned to you.</p>
                    <a href="#" class="create mt-2" style="margin-left: auto;">View to-do list</a>
                  </div>
                </div>
                <div class="col">
                  <div class="d-grid gap-2 col-13 mx-auto mb-4">
                    <a class="announce" type="button" href="#" style="text-decoration: none;">
                      Announce something to your class.
                    </a>
                  </div>
                  <?php
                  $sql_material = "SELECT material_id, title, date FROM classwork_material WHERE teacher_id=? AND class_name=?";
                  $stmt_titles_material = $db->prepare($sql_material);
                  $stmt_titles_material->execute([$teacher_id, $class_name]);

                  $sql_question = "SELECT question_id, title, date FROM classwork_question WHERE teacher_id=? AND class_name=?";
                  $stmt_titles_question = $db->prepare($sql_question);
                  $stmt_titles_question->execute([$teacher_id, $class_name]);

                  $sql_assignment = "SELECT assignment_id, title, date FROM classwork_assignment WHERE teacher_id=? AND class_name=?";
                  $stmt_titles_assignment = $db->prepare($sql_assignment);
                  $stmt_titles_assignment->execute([$teacher_id, $class_name]);

                  foreach ($stmt_titles_material as $title_row) {
                    $material_id = $title_row['material_id'];
                    $title = $title_row['title'];
                    $words = explode(' ', $title);
                    $maxWords = 6;
                    $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                    $date = $title_row['date'];
                    $formatted_date = date("F j", strtotime($date));

                    if (count($words) > $maxWords) {
                      $truncatedTitle .= '...';
                    }

                    ?>
                    <div class="d-grid gap-2 col-13 mx-auto mb-4">
                      <a class="announce" type="button"
                        href="material_course.php?class_id=<?php echo $class_id ?>&material_id=<?php echo $material_id ?>&user_id=<?php echo $user_id ?>"
                        style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div
                          style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                          <i class="bi bi-journal-text" style="color: white; line-height: 42px; font-size: 25px;"></i>
                        </div>
                        <p
                          style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                          <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                          posted a new material:
                          <?php echo $truncatedTitle ?>
                        </p>
                        <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                          <?php echo $formatted_date ?>
                        </div>
                      </a>
                    </div>
                    <?php
                  }

                  foreach ($stmt_titles_question as $question_row) {
                    $question_id = $question_row['question_id'];
                    $title = $question_row['title'];
                    $words = explode(' ', $title);
                    $maxWords = 6;
                    $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                    $date = $question_row['date'];
                    $formatted_date = date("F j", strtotime($date));

                    if (count($words) > $maxWords) {
                      $truncatedTitle .= '...';
                    }

                    ?>
                    <div class="d-grid gap-2 col-13 mx-auto mb-4">
                      <a class="announce" type="button"
                        href="question_course.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $question_id ?>&user_id=<?php echo $user_id ?>"
                        style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div
                          style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                          <i class="bi bi-question-square" style="color: white; line-height: 42px; font-size: 25px;"></i>
                        </div>
                        <p
                          style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                          <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                          posted a new question:
                          <?php echo $truncatedTitle ?>
                        </p>
                        <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                          <?php echo $formatted_date ?>
                        </div>
                      </a>
                    </div>
                    <?php
                  }
                  foreach ($stmt_titles_assignment as $title_row) {
                    $assignment_id = $title_row['assignment_id'];
                    $title = $title_row['title'];
                    $words = explode(' ', $title);
                    $maxWords = 6;
                    $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));
                    $date = $title_row['date'];
                    $formatted_date = date("F j", strtotime($date));

                    if (count($words) > $maxWords)
                      ; {
                      $truncatedTitle .= '...';
                    }

                    ?>
                    <div class="d-grid gap-2 col-13 mx-auto mb-4">
                      <a class="announce" type="button"
                        href="assignment_course.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>"
                        style="text-decoration: none; height: 11vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <div
                          style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: -10px; margin-right: 10px; margin-top: -10px;">
                          <i class="bi bi-journal-text" style="color: white; line-height: 42px; font-size: 25px;"></i>
                        </div>
                        <p
                          style="font-size: 17px; margin-top: -36px; margin-left: 7vh; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                          <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>
                          posted a new assignment:
                          <?php echo $truncatedTitle ?>
                        </p>
                        <div style="margin-left: 45px; margin-top: -10px; font-size: 14px;">
                          <?php echo $formatted_date ?>
                        </div>
                      </a>
                    </div>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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