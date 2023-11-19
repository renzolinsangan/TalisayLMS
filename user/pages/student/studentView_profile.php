<?php
session_start();
include("db_conn.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql_profile = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $conn->prepare($sql_profile);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile);
$stmt->fetch();
$stmt->close();

if (isset($_GET['user_id'])) {
  $getUser_id = $_GET['user_id'];
}

$otherUser_id = $getUser_id;

$sql = "SELECT profile FROM user_profile WHERE user_id = ? AND profile_status = 'recent'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $otherUser_id);
$stmt->execute();
$stmt->bind_result($otherProfile);
$stmt->fetch();
$stmt->close();

$sql_student_info = "SELECT address, firstname, middlename, lastname, grade_level, department, section, usertype FROM user_account WHERE user_id=?";
$stmt = $conn->prepare($sql_student_info);

if ($stmt) {
  $stmt->bind_param("i", $otherUser_id);
  $stmt->execute();
  $stmt->bind_result($address, $firstname, $middlename, $lastname, $grade_level, $department, $section, $usertype);
  $stmt->fetch();
  $stmt->close();
}

if (isset($_POST['add_friend'])) {
  $user_id = $_SESSION['user_id'];
  $friend_id = $_GET['user_id'];
  $firstLetterOfMiddlename = ucfirst(substr($middlename, 0, 1));
  $name = $firstname . ' ' . $firstLetterOfMiddlename . '. ' . $lastname;

  $sql_addFriend = "INSERT INTO friend (user_id, friend_id, name, date) VALUES (?, ?, ?, NOW())";
  $stmt_addFriend = $conn->prepare($sql_addFriend);
  $result = $stmt_addFriend->execute([$user_id, $friend_id, $name]);
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
  <link rel="stylesheet" href="assets/css/notification.css">
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

            $fullName = getFullName($db, $user_id);
            $studentFullName = ($fullName['firstname'] . ' ' . $fullName['lastname']);

            $resultNewsNotif = getNewsNotifications($db);
            $resultFriendNotif = getFriendNotifications($db, $user_id);
            $resultTeacherNotif = getTeacherNotifications($db, $user_id);
            $resultMaterialNotif = getMaterialNotifications($db, $studentFullName);
            $resultQuestionNotif = getQuestionNotification($db, $studentFullName);
            $resultAssignmentNotif = getAssignmentNotification($db, $studentFullName);
            $resultQuizNotif = getQuizNotification($db, $studentFullName);
            $resultExamNotif = getExamNotification($db, $studentFullName);
            $resultQuestionGradeNotif = getQuestionScoreNotification($db, $user_id);
            $resultAssignmentGradeNotif = getAssignmentScoreNotification($db, $user_id);
            $resultQuizGradeNotif = getQuizScoreNotification($db, $user_id);
            $resultExamGradeNotif = getExamScoreNotification($db, $user_id);

            $allNotifications = array_merge(
              $resultNewsNotif,
              $resultFriendNotif,
              $resultTeacherNotif,
              $resultMaterialNotif,
              $resultQuestionNotif,
              $resultAssignmentNotif,
              $resultQuizNotif,
              $resultExamNotif,
              $resultQuestionGradeNotif,
              $resultAssignmentGradeNotif,
              $resultQuizGradeNotif,
              $resultExamGradeNotif
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

                        if ($current_date > $end_date) {
                          header('Location: index.php');
                          exit();
                        }
                        ?>
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
                            <div class="material-notification clickable"
                            onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal"
                              onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a material in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'question'): ?>
                            <div class="question-notification clickable"
                            onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal"
                              onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a question in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'assignment'): ?>
                            <div class="assignment-notification clickable"
                            onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal"
                              onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted an assignment in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'quiz'): ?>
                            <div class="quiz-notification clickable"
                            onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal"
                              onclick="window.location.href='course.php'">
                                <?php echo $teacherName; ?> posted a quiz in
                                <?php echo $notification['class_name']; ?>.
                              </h6>
                            </div>
                          <?php elseif ($notification['notification_type'] === 'exam'): ?>
                            <div class="exam-notification clickable"
                            onclick="window.location.href='course.php'">
                              <h6 class="preview-subject font-weight-normal">
                                <?php echo $teacherName; ?> posted an exam in
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
                        <h6 class="preview-subject font-weight-normal"
                        onclick="window.location.href='course.php'">
                          <?php if ($notification['scoreNotification_type'] === 'questionGrade'): ?>
                            <?php echo $notification['teacherFirstName'] ?>
                            posted your score in
                            <?php echo $notification['questionTitle']; ?>
                            (question).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='course.php'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'assignmentGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['assignmentTitle']; ?>
                          (assignment).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='course.php'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'quizGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['quizTitle']; ?>
                          (quiz).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='course.php'">
                            on
                            <?php echo date('F j', strtotime($notification['date'])); ?>
                          </p>
                        <?php elseif ($notification['scoreNotification_type'] === 'examGrade'): ?>
                          <?php echo $notification['teacherFirstName'] ?>
                          posted your score in
                          <?php echo $notification['examTitle']; ?>
                          (exam).
                          </h6>
                          <p class="font-weight-light small-text mb-0 text-muted"
                          onclick="window.location.href='course.php'">
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
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row justify-content-between">
                    <div class="col-md-3">
                      <div class="circle-image"
                        style="margin-left: 20px; background-image: url('<?php echo empty($otherProfile) ? 'images/profile.png' : 'assets/image/' . $otherProfile; ?>');">
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
                        <?php
                        include("config.php");

                        if (isset($_POST['unfriend'])) {
                          $sql_deleteFriendship = "DELETE FROM friend WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
                          $stmt_deleteFriendship = $db->prepare($sql_deleteFriendship);
                          $stmt_deleteFriendship->execute([$user_id, $otherUser_id, $otherUser_id, $user_id]);
                        }

                        $isFriend = false;

                        $sql_addedFriend = "SELECT friend_id FROM friend WHERE user_id = ? AND friend_id = ?";
                        $stmt_addedFriend = $db->prepare($sql_addedFriend);
                        $result = $stmt_addedFriend->execute([$user_id, $otherUser_id]);

                        if ($stmt_addedFriend->rowCount() > 0) {
                          $isFriend = true;
                        }
                        ?>
                        <div></div>
                        <div class="text-right">
                          <?php if ($isFriend): ?>
                            <button class="btn unfriend" type="button" data-bs-toggle="modal"
                              data-bs-target="#staticBackdropUnfriend" style="color: red;">
                              <i class="bi bi-trash"></i> Unfriend
                            </button>
                          <?php else: ?>
                            <button class="btn add-friend" type="button" data-bs-toggle="modal"
                              data-bs-target="#staticBackdropAddFriend" style="color: green;">
                              + Add Friend
                            </button>
                          <?php endif; ?>
                        </div>
                        <form action="" method="post">
                          <div class="modal fade" id="staticBackdropUnfriend" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header" style="border: none;">
                                  <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                    Unfriend
                                  </h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <h3>Remove
                                    <?php echo $firstname . ' ' . $lastname ?> from your friends.
                                  </h3>
                                  <p class="text-body-secondary">If you wish to cancel, press the x button.</p>
                                </div>
                                <div class="modal-footer" style="border: none;">
                                  <button type="submit" name="unfriend" class="btn btn-danger">Unfriend</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </form>
                        <form action="" method="post">
                          <div class="modal fade" id="staticBackdropAddFriend" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header" style="border: none;">
                                  <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                    Add Friend
                                  </h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <h3>Add
                                    <?php echo $firstname . ' ' . $lastname ?> as your friend.
                                  </h3>
                                  <p class="text-body-secondary">If you wish to cancel, press the x button.</p>
                                </div>
                                <div class="modal-footer" style="border: none;">
                                  <button type="submit" name="add_friend" class="btn btn-success">Add Friend</button>
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
                            <p>Section:
                              <?php echo $section ?>
                            </p>
                          </li>
                          <li>
                            <p>Year Level:
                              <?php echo $grade_level ?>
                            </p>
                          </li>
                          <li>
                            <p>House Address:
                              <?php echo $address ?>
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