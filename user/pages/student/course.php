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
    if ($row['section'] === $student_section) {
      $_SESSION['class_id'] = $row['class_id'];
      $_SESSION['class_name'] = $row['class_name'];
      $_SESSION['section'] = $row['section'];
      $_SESSION['subject'] = $row['subject'];
      $_SESSION['strand'] = $row['strand'];
      $_SESSION['teacher_id'] = $row['teacher_id'];
      $_SESSION['first_name'] = $row['first_name'];
      $_SESSION['last_name'] = $row['last_name'];

      $sql_insert_enrollment = "INSERT INTO class_enrolled (tc_id, class_name, section, subject, grade_level, strand, teacher_id, 
      class_code, first_name, last_name, student_id, student_firstname, student_lastname, date) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
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
      header("Location: course.php?msg=You are not from that class, please try again!");
      exit;
    }
  } else {
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS User</title>
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/course.css">
  <link rel="stylesheet" href="assets/css/notification.css">
  <link rel="shortcut icon" href="assets/image/trace.svg" />
</head>

<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="images/trace.svg" class="mr-2"
            alt="logo" />Talisay LMS</a>
        <a class="navbar-brand brand-logo-mini" href="index.php"><img src="assets/image/trace.svg" alt="logo" /></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown mt-2" style="margin-right: -2px; cursor: pointer;">
            <a class="nav-link dropdown-toggle" data-toggle="modal" data-target="#myModal">
              <i class="bi bi-plus-square mx-0" style="font-size: 28px; color: green;"></i>
            </a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
              data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <?php
            include("config.php");
            include("notifications.php");

            $fullName = getFullName($db, $user_id);
            $studentFullName = ($fullName['firstname'] . ' ' . $fullName['lastname']);

            $resultNewsNotif = getNewsNotifications($db);
            $resultFriendNotif = getFriendNotifications($db, $user_id);
            $resultTeacherNotif = getTeacherNotifications($db, $user_id);
            $resultMaterialNotif = getMaterialNotifications($db, $studentFullName);
            $resultQuestionNotif = getQuestionNotification($db, $studentFullName);
            $resultAssignmentNotif = getAssignmentNotification($db, $studentFullName);
            $resultQuestionGradeNotif = getQuestionScoreNotification($db, $user_id);
            $resultAssignmentGradeNotif = getAssignmentScoreNotification($db, $user_id);

            $allNotifications = array_merge(
              $resultNewsNotif,
              $resultFriendNotif,
              $resultTeacherNotif,
              $resultMaterialNotif,
              $resultQuestionNotif,
              $resultAssignmentNotif,
              $resultQuestionGradeNotif,
              $resultAssignmentGradeNotif,
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
                      <?php if (isset($notification['title'])): ?>
                        <div class="preview-icon bg-success">
                          <i class="ti-info-alt mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['friend_id'])): ?>
                        <div class="preview-icon bg-warning">
                          <i class="ti-user mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['teacher_id'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-book mx-0"></i>
                        </div>
                      <?php elseif (isset($notification['score'])): ?>
                        <div class="preview-icon bg-info">
                          <i class="ti-pencil mx-0"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="preview-item-content">
                      <?php if (isset($notification['title'])): ?>
                        <?php
                        $link = ($notification['type'] === 'news') ? 'news.php' : 'announcement.php';

                        $end_date = $notification['end_date'];
                        $current_date = date('Y-m-d H:i:s');
                        $end_date_passed = ($current_date > $end_date);
                        ?>

                        <?php if ($end_date_passed): ?>
                          <h6 class="preview-subject font-weight-normal">
                            <?php echo $notification['title']; ?> (
                            <?php echo ucfirst($notification['type']); ?>)
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            by
                            <?php echo $notification['name']; ?> on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php else: ?>
                          <h6 class="preview-subject font-weight-normal"
                            onclick="window.location.href='view_<?php echo $link ?>?news_id=<?php echo $notification['news_id'] ?>'">
                            <?php echo $notification['title']; ?> (
                            <?php echo ucfirst($notification['type']); ?>)
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted"
                            onclick="window.location.href='view_<?php echo $link ?>?news_id=<?php echo $notification['news_id'] ?>'">
                            by
                            <?php echo $notification['name']; ?> on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php endif; ?>
                      <?php elseif (isset($notification['friend_id'])): ?>
                        <?php
                        $friendNameParts = explode(' ', $notification['name']);
                        $firstName = $friendNameParts[0];
                        ?>
                        <h6 class="preview-subject font-weight-normal" onclick="window.location.href='friends.php'">
                          You added
                          <?php echo $firstName; ?> as your friend.
                        </h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          on
                          <?php echo date('F j', strtotime($notification['date'])); ?>
                        </p>
                      <?php elseif (isset($notification['teacher_id'])): ?>
                        <?php
                        $sqlTeacherName = "SELECT firstname FROM user_account WHERE user_id = :teacher_id";
                        $stmtTeacherName = $db->prepare($sqlTeacherName);
                        $stmtTeacherName->bindParam(':teacher_id', $notification['teacher_id']);
                        $stmtTeacherName->execute();
                        $teacherName = $stmtTeacherName->fetchColumn();
                        ?>
                        <?php if ($notification['notification_type'] === 'teacher'): ?>
                          <div class="preview-item-content" onclick="window.location.href='teacher.php'">
                            <h6 class="preview-subject font-weight-normal" onclick="window.location.href='teacher.php'">
                              You added
                              <?php echo $teacherName; ?> as your teacher.
                            </h6>
                            <p class="font-weight-light small-text mb-0 text-muted"
                              onclick="window.location.href='teacher.php'">
                              on
                              <?php echo date('F j', strtotime($notification['date'])); ?>
                            </p>
                          </div>
                        <?php else: ?>
                          <?php if ($notification['notification_type'] === 'material'): ?>
                            <div class="material-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a material in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'question'): ?>
                            <div class="question-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a question in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'assignment'): ?>
                            <div class="assignment-notification clickable" onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted an assignment in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php endif; ?>
                          <p class="font-weight-light small-text mb-0 text-muted">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php endif; ?>
                      <?php elseif (isset($notification['score'])): ?>
                        <h6 class="preview-subject font-weight-normal" onclick="window.location.href='course.php'">
                          <?php if ($notification['scoreNotification_type'] === 'questionGrade'): ?>
                            <?php echo $notification['teacherFirstName'] ?>
                            posted your score in
                            <?php echo $notification['questionTitle']; ?>
                            (question).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted" onclick="window.location.href='course.php'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'assignmentGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['assignmentTitle']; ?>
                          (assignment).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted" onclick="window.location.href='course.php'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php
            ?>
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
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col mb-2">
              <h2>Enrolled Ongoing Courses</h2>
              <p class="text-body-secondary">(Student)</p>
            </div>
          </div>
          <div class="modal fade" id="myModal">
            <form action="" method="post" class="forms-sample" id="myForm">
              <div class="modal-dialog modal-dialog-centered" style="top: -6vh;">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Join Class</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <div class="col-mb-4">
                      <div id="validationAlert" class="alert alert-danger alert-dismissible fade show" role="alert"
                        style="display: none;">
                        Please fill in all required fields.
                      </div>
                    </div>
                    <div class="form-floating mb-3">
                      <h3>Class Code</h3>
                      <p class="text-body-secondary">Ask your teacher for the class code, then enter it here.</p>
                    </div>
                    <div class="form-floating mb-4">
                      <input type="text" name="class_code" class="form-control" id="floatingInput"
                        placeholder="Class Code">
                      <label for="floatingName">Class Code</label>
                    </div>
                    <div class="form-floating">
                      <h6>To sign in with a class code</h6>
                      <ul style="list-style-type: disc;">
                        <li class="text-body-secondary">Authorized account only prohibited</li>
                        <li class="text-body-secondary">Use a class code with 5-7 letters or numbers, and no spaces or
                          symbols</li>
                      </ul>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="submit_code" class="btn btn-success">Submit</button>
                  </div>
                </div>
              </div>
            </form>
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

            $sql_enrolled = "SELECT ce.class_id, ce.tc_id, se.class_name, se.section, se.first_name, se.last_name, se.teacher_id
                FROM class_enrolled ce
                INNER JOIN section se ON ce.class_code = se.class_code
                WHERE ce.student_id = ? AND ce.archive_status = '' AND se.archive_status = ''";
            $stmt_enrolled = $conn->prepare($sql_enrolled);
            $stmt_enrolled->bind_param("i", $student_id);
            $stmt_enrolled->execute();
            $result = $stmt_enrolled->get_result();
            ?>
            <?php
            if ($result->num_rows > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                $_SESSION['class_id'] = $row['class_id'];
                $_SESSION['tc_id'] = $row['tc_id'];
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
                    <a href="class_course.php?class_id=<?php echo $row['class_id']; ?>&tc_id=<?php echo $row['tc_id'] ?>"
                      class="course">
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
            } else {
              ?>
              <div class="col-md-4">
                <div class="card">
                  <div class="card-body">
                    <h3>No Enrolled Courses.</h3>
                    <p class="text-body-secondary">Join from the course subject using the class code provided by your
                      teachers.</p>
                    <p style="color: green;">Click the + button from the header to encode the class code.</p>
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
    <script src="../../vendors/js/vendor.bundle.base.js"></script>
    <script src="../../js/off-canvas.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/settings.js"></script>
    <script src="../../js/todolist.js"></script>
</body>

</html>