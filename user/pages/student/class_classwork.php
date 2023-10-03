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
  <link rel="stylesheet" href="assets/css/class_cw.css">
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
              <a href="class_course.php?class_id=<?php echo $class_id ?>" class="people"
                style="margin-left: 2vh;">Stream</a>
              <a href="class_classwork.php?class_id=<?php echo $class_id ?>" class="nav-link active">Classwork</a>
              <a href="class_people.php?class_id=<?php echo $class_id ?>" class="people">People</a>
              <?php
            }
            ?>
          </div>
        </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12" style="margin-top: 10vh;">
              <div class="row">
                <?php
                include("db_conn.php");
                $sql = "SELECT class_topic FROM topic WHERE teacher_id=? AND class_name=?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$teacher_id, $class_name]);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $_SESSION['class_topic'] = $row['class_topic'];
                  ?>
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card" style="border-radius: 0px;">
                      <div class="topic-header mt-3 d-flex align-items-center justify-content-between">
                        <h2 class="topic_title mb-3" style="margin-left: 5vh; color: green;">
                          <?php echo ucfirst($row['class_topic']) ?>
                        </h2>
                        <div class="dropdown mb-3">
                          <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="font-size: 20px; color: green; margin-right: 10px;">
                            <i class="bi bi-three-dots-vertical"></i>
                          </a>

                          <ul class="dropdown-menu">
                            <li>
                              <a class="dropdown-item" href="#">Copy Link</a>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <p class="bottom-border"></p>
                      <div class="topic-body">
                        <?php
                        $sql_material = "SELECT material_id, title FROM classwork_material WHERE class_topic=? AND teacher_id=? AND class_name=?";
                        $stmt_titles_material = $db->prepare($sql_material);
                        $stmt_titles_material->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

                        $sql_assignment = "SELECT assignment_id, title FROM classwork_assignment WHERE class_topic=? AND teacher_id=? AND class_name=?";
                        $stmt_titles_assignment = $db->prepare($sql_assignment);
                        $stmt_titles_assignment->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

                        $sql_question = "SELECT question_id, title FROM classwork_question WHERE class_topic=? AND teacher_id=? AND class_name=?";
                        $stmt_titles_question = $db->prepare($sql_question);
                        $stmt_titles_question->execute([$_SESSION['class_topic'], $teacher_id, $class_name]);

                        foreach ($stmt_titles_material as $title_row) {
                          $material_id = $title_row['material_id'];
                          $title = $title_row['title'];
                          $words = explode(' ', $title);
                          $maxWords = 4;
                          $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));

                          if (count($words) > $maxWords) {
                            $truncatedTitle .= '...';
                          }
                          ?>
                          <div class="topic-material">
                            <div class="col-12 grid-margin strech-card">
                              <div class="body-card"
                                style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                                <div style="display: flex; align-items: center;">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-journal-text"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 15px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="dropdown">
                                  <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="font-size: 20px; color: green; margin-right: 10px;">
                                    <i class="bi bi-three-dots-vertical"></i>
                                  </a>

                                  <ul class="dropdown-menu">
                                    <li>
                                      <a class="dropdown-item" href="">Copy Link</a>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                          <?php
                        }
                        foreach ($stmt_titles_assignment as $titlerow) {
                          $assignment_id = $titlerow['assignment_id'];
                          $title = $titlerow['title'];
                          $words = explode(' ', $title);
                          $maxWords = 4;
                          $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));

                          if (count($words) > $maxWords) {
                            $truncatedTitle .= '...';
                          }
                          ?>
                          <div class="topic-assignment">
                            <div class="col-12 grid-margin strech-card">
                              <div class="body-card"
                                style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                                <div style="display: flex; align-items: center;">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-journal-text"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 15px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="dropdown">
                                  <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="font-size: 20px; color: green; margin-right: 10px;">
                                    <i class="bi bi-three-dots-vertical"></i>
                                  </a>

                                  <ul class="dropdown-menu">
                                    <li>
                                      <a class="dropdown-item" href="">Copy Link</a>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                          <?php
                        }
                        foreach ($stmt_titles_question as $rowquestion) {
                          $question_id = $rowquestion['question_id'];
                          $title = $rowquestion['title'];
                          $words = explode(' ', $title);
                          $maxWords = 4;
                          $truncatedTitle = implode(' ', array_slice($words, 0, $maxWords));

                          if (count($words) > $maxWords) {
                            $truncatedTitle .= '...';
                          }
                          ?>
                          <div class="topic-question">
                            <div class="col-12 grid-margin strech-card">
                              <div class="body-card"
                                style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                                <div style="display: flex; align-items: center;">
                                  <div
                                    style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px;">
                                    <i class="bi bi-question-square"
                                      style="color: white; line-height: 41px; font-size: 26px;"></i>
                                  </div>
                                  <p style="margin-top: 15px; margin-left: 30px; font-size: 17px; color: black;">
                                    <?php echo $truncatedTitle ?>
                                  </p>
                                </div>
                                <div class="dropdown">
                                  <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="font-size: 20px; color: green; margin-right: 10px;">
                                    <i class="bi bi-three-dots-vertical"></i>
                                  </a>

                                  <ul class="dropdown-menu">
                                    <li>
                                      <a class="dropdown-item" href="">Copy Link</a>
                                    </li>
                                  </ul>
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
                  <?php
                }
                ?>
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