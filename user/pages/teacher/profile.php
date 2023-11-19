<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile);
$stmt->fetch();
$stmt->close();

if (isset($_POST['submit'])) {
  if (isset($_FILES['profile']) && !empty($_FILES['profile']['name'])) {
    $upload_directory = 'assets/image/';
    if ($_FILES['profile']['error'] === UPLOAD_ERR_OK) {
      $file_name = basename($_FILES['profile']['name']);
      $target_path = $file_name;
      if (move_uploaded_file($_FILES['profile']['tmp_name'], $upload_directory . $file_name)) {
        $update_status_sql = "UPDATE user_profile SET profile_status = 'old' WHERE user_id = ?";
        $stmt_update_status = $conn->prepare($update_status_sql);
        $stmt_update_status->execute([$user_id]);

        $insert_sql = "INSERT INTO user_profile (user_id, profile, profile_status) VALUES (?, ?, 'recent')";
        $stmt_insert = $conn->prepare($insert_sql);
        $result = $stmt_insert->execute([$user_id, $target_path]);

        if ($result) {
          header("Location: profile.php");
          exit;
        } else {
          echo "Error updating profile picture.";
        }
      } else {
        echo "Error uploading the profile picture.";
      }
    } else {
      echo "Error: Profile picture upload failed with error code " . $_FILES['profile']['error'];
    }
  }
}

$sql_student_info = "SELECT email, address, contact, firstname, middlename, lastname, department, usertype FROM user_account WHERE user_id=?";
$stmt = $conn->prepare($sql_student_info);

if ($stmt) {
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($email, $address, $contact, $firstname, $middlename, $lastname, $department, $usertype);
  $stmt->fetch();
  $stmt->close();
}
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
  <link rel="stylesheet" href="assets/css/profile.css">
  <link rel="stylesheet" href="assets/css/notif.css">
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
            <?php
            include("config.php");
            include("notifications.php");

            $resultNewsNotif = getNewsNotifications($db);
            $resultStudentNotif = getStudentNotifications($db, $user_id);
            $resultQuestionNotif = getQuestionNotifications($db, $user_id);
            $resultAssignmentNotif = getAssignmentNotifications($db, $user_id);
            $resultQuizNotif = getQuizNotifications($db, $user_id);
            $resultExamNotif = getExamNotifications($db, $user_id);
            $resultClassroomNotif = getClassroomNotifications($db, $user_id);

            $allNotifications = array_merge(
              $resultNewsNotif,
              $resultStudentNotif,
              $resultQuestionNotif,
              $resultAssignmentNotif,
              $resultQuizNotif,
              $resultExamNotif,
              $resultClassroomNotif
            );

            usort($allNotifications, function ($a, $b) {
              return strtotime($b['date']) - strtotime($a['date']);
            });
            ?>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
              aria-labelledby="notificationDropdown">
              <div class="scrollable-notifications">
                <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                <?php foreach ($allNotifications as $notification): ?>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <?php if (isset($notification['title']) && isset($notification['type'])): ?>
                        <div class="preview-icon bg-success">
                          <i class="ti-info-alt mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['student_id'])): ?>
                        <div class="preview-icon bg-warning">
                          <i class="ti-user mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['class_name'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-blackboard mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['question_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['assignment_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['quiz_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['exam_course_status'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="preview-item-content">
                      <?php if (isset($notification['title']) && isset($notification['type'])): ?>
                        <?php
                        $link = '';

                        $currentDate = new DateTime();
                        $endDate = new DateTime($notification['end_date']);
                        if ($currentDate > $endDate) {
                          $link = 'index.php';
                        } else {
                          if ($notification['type'] === 'announcement') {
                            $link = 'view_announcement.php' . $notification['news_id'];
                          } elseif ($notification['type'] === 'news') {
                            $link = 'view_news.php?news_id=' . $notification['news_id'];
                          }
                        }
                        ?>
                        <h6 class="preview-subject font-weight-normal"
                          onclick="window.location.href='<?php echo $link; ?>';">
                          <?php echo $notification['title']; ?> (
                          <?php echo ucfirst($notification['type']); ?>)
                        </h6>
                        <p class="font-weight-light small-text mb-0 text-muted"
                        onclick="window.location.href='<?php echo $link; ?>';">
                          by
                          <?php echo $notification['name']; ?> on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['student_id'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['student_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal" 
                        onclick="window.location.href='student.php'">
                          You added
                          <?php echo $studentName; ?> as student.
                        </h6>
                        <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='student.php'">
                          on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['class_name'])): ?>
                        <div class="preview-item-content"
                          onclick="window.location.href='class_people.php?class_id=<?php echo $notification['tc_id'] ?>'">
                          <h6 class="preview-subject font-weight-normal">
                            <?php echo $notification['student_firstname']; ?> joined from
                            <?php echo $notification['class_name']; ?>
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </div>
                      <?php elseif (isset($notification['question_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                          <h6 class="preview-subject font-weight-normal"
                          onclick="window.location.href='question_review.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $notification['question_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['question_course_status']; ?>
                          <?php echo $notification['title']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='question_review.php?class_id=<?php echo $class_id ?>&question_id=<?php echo $notification['question_id'] ?>'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php elseif (isset($notification['assignment_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal"
                        onclick="window.location.href='assignment_review.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $notification['assignment_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['assignment_course_status']; ?>
                          <?php echo $notification['title']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='assignment_review.php?class_id=<?php echo $class_id ?>&assignment_id=<?php echo $notification['assignment_id'] ?>'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php elseif (isset($notification['quiz_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal"
                        onclick="window.location.href='quiz_review.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $notification['quiz_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['quiz_course_status']; ?>
                          <?php echo $notification['quizTitle']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='quiz_review.php?class_id=<?php echo $class_id ?>&quiz_id=<?php echo $notification['quiz_id'] ?>'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php elseif (isset($notification['exam_course_status'])): ?>
                        <?php
                        $sqlStudentName = "SELECT firstname FROM user_account WHERE user_id = :user_id";
                        $stmtStudentName = $db->prepare($sqlStudentName);
                        $stmtStudentName->bindParam(':user_id', $notification['user_id']);
                        $stmtStudentName->execute();
                        $studentName = $stmtStudentName->fetchColumn();
                        ?>
                        <h6 class="preview-subject font-weight-normal"
                        onclick="window.location.href='exam_review.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $notification['exam_id'] ?>'">
                          <?php echo $studentName; ?>
                          <?php echo $notification['exam_course_status']; ?>
                          <?php echo $notification['examTitle']; ?>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='exam_review.php?class_id=<?php echo $class_id ?>&exam_id=<?php echo $notification['exam_id'] ?>'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        </h6>
                      <?php endif; ?>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
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
                    href="student_report.php?user_id=<?php echo $user_id ?>">Student Reports</a></li>
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
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row justify-content-between">
                    <div class="col-md-3">
                      <div class="circle-image"
                        style="margin-left: 20px; background-image: url('<?php echo empty($profile) ? 'images/profile.png' : 'assets/image/' . $profile; ?>');">
                      </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center mt-3">
                      <div class="col-md-12">
                        <h2>
                          <?php echo $firstname . " " . (!empty($middlename) ? strtoupper(substr($middlename, 0, 1)) . "." : "") . " " . $lastname ?>
                        </h2>
                        <h3 class="mt-2" style="color: green;">
                          <?php echo strtoupper($department) ?>
                        </h3>
                        <p class="text-body-secondary">(
                          <?php echo $usertype ?>)
                        </p>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="d-flex flex-column justify-content-between h-100">
                        <div></div>
                        <div class="text-right">
                          <button class="btn edit" type="button" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">
                            Edit Profile
                          </button>
                          <form action="" method="post" enctype="multipart/form-data">
                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static"
                              data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                              aria-hidden="true">
                              <div class="modal-dialog" style="width: 50vh; margin-top: 25vh;">
                                <div class="modal-content">
                                  <div class="modal-body">
                                    <div class="text-start mb-3">
                                      <label for="fileInput" class="form-label mb-3">Upload Profile</label>
                                      <input type="file" name="profile" class="form-control" id="fileInput"
                                        accept="image/*" capture="camera">
                                    </div>
                                    <div class="modal-button mt-3 d-flex justify-content-end align-items-end">
                                      <button type="button" class="btn" data-bs-dismiss="modal"
                                        style="margin-right: 2vh; padding: 0; outline: none;">Cancel</button>
                                      <button type="submit" class="btn" name="submit"
                                        style="color: green; margin-top: 2vh; padding: 0; outline: none;">Submit</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row justify-content-left align-items-center">
                      <div class="col-md-3">
                        <h3>
                          <?php echo ucfirst($usertype) ?> Information
                        </h3>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <hr class="mt-3" style="border-top: 2px solid black;">
                      </div>
                    </div>
                    <div class="row justify-content-left align-items-center">
                      <div class="col-md-5">
                        <h3 class="mb-3">Basic Information</h3>
                        <ul>
                          <li>
                            <p>Department:
                              <?php echo strtoupper($department) ?>
                            </p>
                          </li>
                          <li>
                            <p>House Address:
                              <?php echo $address ?>
                            </p>
                          </li>
                        </ul>
                        <h3 class="mt-4">Contact Information</h3>
                        <ul>
                          <li>
                            <p>Email Address:
                              <?php echo $email ?>
                            </p>
                          </li>
                          <li>
                            <p>Contact Number:
                              <?php echo $contact ?>
                            </p>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
      <script src="../../vendors/js/vendor.bundle.base.js"></script>
      <script src="../../js/off-canvas.js"></script>
      <script src="../../js/hoverable-collapse.js"></script>
      <script src="../../js/template.js"></script>
      <script src="../../js/settings.js"></script>
      <script src="../../js/todolist.js"></script>
</body>

</html>