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
  $written = $_POST['written'];
  $performance = $_POST['performance'];
  $exam = $_POST['exam'];
  $basegrade = $_POST['basegrade'];

  $sql = "INSERT INTO section (class_name, section, subject, strand, teacher_id, class_code, first_name, 
  last_name, written, performance, exam, basegrade) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmtinsert = $db->prepare($sql);
  $result = $stmtinsert->execute([
    $class_name,
    $section,
    $subject,
    $strand,
    $teacher_id,
    $class_code,
    $first_name,
    $last_name,
    $written,
    $performance,
    $exam,
    $basegrade
  ]);

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
if (isset($_POST['archive'])) {
  $class_id = $_POST['class_id'];

  $sql_update_section = "UPDATE section SET archive_status = 'archive' WHERE class_id = ?";
  $stmt_update_section = $db->prepare($sql_update_section);
  $stmt_update_section->execute([$class_id]);

  if ($stmt_update_section->rowCount() > 0) {
    $sql_update_classEnrolled = "UPDATE class_enrolled SET archive_status = 'archive' WHERE tc_id = ?";
    $stmt_update_classEnrolled = $db->prepare($sql_update_classEnrolled);
    $stmt_update_classEnrolled->execute([$class_id]);

    header("Location: archive.php");
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/course.css">
  <link rel="stylesheet" href="assets/css/notif.css">
  <link rel="shortcut icon" href="assets/image/trace.svg" />
</head>

<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="images/trace.svg" class="mr-2"
            alt="logo" />Talisay LMS</a>
        <a class="navbar-brand brand-logo-mini" href="index.php"><img src="images/trace.svg" alt="logo" /></a>
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
            <div class="modal fade" id="myModal">
              <form action="" method="post" class="forms-sample" id="myForm">
                <div class="modal-dialog modal-dialog-centered" style="top: -6vh;">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Create Class List</h5>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                      <div class="col-mb-4">
                        <div id="validationAlert" class="alert alert-danger alert-dismissible fade show" role="alert"
                          style="display: none;">
                          Please fill in all required fields.
                        </div>
                      </div>
                      <div class="form-floating mb-4">
                        <select name="strand" class="form-control custom-select lightened-select"
                          style="padding-top: 2vh;">
                          <option disabled selected value="">Select Department</option>
                          <option value="STEM">STEM</option>
                          <option value="HUMSS">HUMSS</option>
                          <option value="ABM">ABM</option>
                          <option value="TVL">TVL</option>
                        </select>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="text" name="section" class="form-control" id="floatingInput" placeholder="Section">
                        <label for="floatingSection">Section</label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="text" name="class_name" class="form-control" id="floatingInput"
                          placeholder="Class Name">
                        <label for="floatingName">Class Name</label>
                      </div>
                      <div class="form-floating mb-4">
                        <input type="text" name="subject" class="form-control" id="floatingInput" placeholder="Subject">
                        <label for="floatingSubject">Subject</label>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group mb-4">
                            <label for="floatingWrittenGrade">Written Works %</label>
                            <input type="text" name="written" class="form-control" id="floatingInput"
                              placeholder="Grade">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group mb-4">
                            <label for="floatingPerformanceGrade">Performance Task %</label>
                            <input type="text" name="performance" class="form-control" id="floatingInput"
                              placeholder="Grade">
                          </div>
                        </div>
                        <div></div>
                        <div class="col-md-6">
                          <div class="form-group mb-4">
                            <label for="floatingExamGrade">Exam %</label>
                            <input type="text" name="exam" class="form-control" id="floatingInput"
                              placeholder="Grade">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <label for="floatingBaseGrade">Base Grade</label>
                          <input type="text" name="basegrade" class="form-control" id="floatingInput"
                            placeholder="Base Grade">
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                      <button type="submit" name="submit" class="btn btn-success">Submit</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="col">
              <h2>
                Ongoing Courses
              </h2>
              <p class="text-body-secondary">
                (Teacher)
              </p>
            </div>
            <div class="col-md-4" style="margin-left: 113vh; margin-bottom: -30px;">
              <div class="row">
                <div class="col">
                  <div class="card-body">
                    <p class="text-body-secondary" style="font-size: 14px; margin-top: -90px;">
                      Create your class list here!
                      <i class="bi bi-box-arrow-up-right" style="font-size: 20px; position: relative; top: -20px;"></i>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <?php
            include("db_conn.php");
            $teacher_id = $_SESSION['user_id'];

            $sql = "SELECT * FROM section WHERE teacher_id = ? AND strand = ? AND archive_status = ''";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $teacher_id, $department);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <?php
            if ($result->num_rows > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                $_SESSION['class_name'] = $row['class_name'];
                $_SESSION['section'] = $row['section'];
                $firstName = ucfirst(strtolower($_SESSION['first_name']));
                $lastName = ucfirst(strtolower($_SESSION['last_name']));
                $_SESSION['teacher_name'] = $firstName . " " . $lastName;

                $class_id = $row['class_id'];
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
                          <h5>Archive <i class="bi bi-archive" style="font-size: 20px;"></i></i></h5>
                        </button>
                        <div class="modal fade" id="staticBackdrop<?php echo $row['class_id']; ?>" data-bs-backdrop="static"
                          data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                          <div class="modal-dialog" style="width: 50vh; margin-top: 25vh;">
                            <div class="modal-content">
                              <div class="modal-body">
                                <div class="text-start">
                                  <h3>Archive
                                    <?php echo $row['class_name'] ?>?
                                  </h3>
                                  <p class="text-body-secondary mt-3">Class will be moved to the archived courses.</p>
                                  <p class="text-body-secondary mt-3">Do you want to archive this class?</p>
                                  <p class="text-body-secondary mt-3">Press click archive button if yes.</p>
                                </div>
                                <input type="hidden" name="class_id" value="<?php echo $row['class_id']; ?>">

                                <div class="modal-button mt-3 d-flex justify-content-end align-items-end">
                                  <button type="button" class="btn" data-bs-dismiss="modal"
                                    style="margin-right: 2vh; padding: 0;">Cancel</button>
                                  <button type="submit" class="btn" name="archive"
                                    style="color: green; margin-top: 2vh; padding: 0;">Archive</button>
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
            } else {
              ?>
              <div class="col-md-4">
                <div class="card">
                  <div class="card-body">
                    <h3>No Created Courses.</h3>
                    <p class="text-body-secondary">Create your course subject to interact with your students.</p>
                    <p style="color: green;">Click the + button from the right-header to input and create course subject.
                    </p>
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
  </div>

  <script>
    var form = document.getElementById('myForm');
    var validationAlert = document.getElementById('validationAlert');

    form.addEventListener('submit', function (event) {
      var classnameInput = form.querySelector('input[name="class_name"]');
      var sectionInput = form.querySelector('input[name="section"]');
      var subjectInput = form.querySelector('input[name="subject"]');
      var strandDropdown = form.querySelector('select[name="strand"]');
      var writtenInput = form.querySelector('input[name="written"]');
      var performanceInput = form.querySelector('input[name="performance"]');
      var examInput = form.querySelector('input[name="exam"]');

      if (classnameInput.value === '' || sectionInput.value === '' ||
        subjectInput.value === '' || strandDropdown.value === '' ||
        writtenInput.value === '' || performanceInput.value === '' ||
        examInput.value === '') {
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

      if (!/^\d+$/.test(writtenInput.value.trim())) {
      event.preventDefault();
      writtenInput.classList.add('is-invalid');
    } else {
      writtenInput.classList.remove('is-invalid');
    }

    if (!/^\d+$/.test(performanceInput.value.trim())) {
      event.preventDefault();
      performanceInput.classList.add('is-invalid');
    } else {
      performanceInput.classList.remove('is-invalid');
    }

    if (!/^\d+$/.test(examInput.value.trim())) {
      event.preventDefault();
      examInput.classList.add('is-invalid');
    } else {
      examInput.classList.remove('is-invalid');
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