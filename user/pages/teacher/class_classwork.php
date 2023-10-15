<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
}

$teacher_id = $_SESSION['user_id'];

if (isset($_POST['submit_topic'])) {
  $class_topic = $_POST['class_topic'];

  $sql_section = "SELECT section, subject, strand, teacher_id, class_name FROM section WHERE class_id=?";
  $stmt_section = $db->prepare($sql_section);
  $stmt_section->execute([$class_id]);
  $section_data = $stmt_section->fetch(PDO::FETCH_ASSOC);

  if ($section_data) {
    $section = $section_data['section'];
    $subject = $section_data['subject'];
    $strand = $section_data['strand'];
    $teacher_id = $section_data['teacher_id'];
    $class_name = $section_data['class_name'];

    $sql_insert_topic = "INSERT INTO topic (class_topic, section, subject, strand, teacher_id, class_id, class_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_topic = $db->prepare($sql_insert_topic);
    $result = $stmt_insert_topic->execute([$class_topic, $section, $subject, $strand, $teacher_id, $class_id, $class_name]);

    if ($result) {
      header("Location: class_classwork.php?class_id=$class_id");
    } else {
      echo "Error inserting topic into the database.";
    }
  } else {
    echo "Error: Section information not found for the given class_id.";
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
  <link rel="stylesheet" href="assets/css/class_classwork.css">
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
        <div class="header-sticky">
          <div class="header-links" style="overflow-x: auto; white-space: nowrap;">
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
            <div class="modal fade" id="myModal">
              <form action="" method="post" class="forms-sample" id="myForm">
                <div class="modal-dialog modal-dialog-centered" style="top: -6vh;">
                  <div class="modal-content">
                    <div class="modal-body">
                      <h4 class="mb-4">Add Class Topic</h4>
                      <div id="validationAlert" class="alert alert-danger" style="display: none;">Class Topic cannot be
                        empty.</div>
                      <div class="form-floating mb-2">
                        <input type="text" name="class_topic" id="class_topic" class="form-control" id="floatingInput"
                          placeholder="Class Name">
                        <label for="floatingName">Class Topic</label>
                      </div>
                    </div>
                    <div class="modal-footer" style="border: none;">
                      <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                      <button type="submit" name="submit_topic" class="btn btn-success">Add</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="row">
            <div class="col-12 grid-margin stretch-card" style="margin-top: 10vh;">
              <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="bi bi-plus" style="font-size: 15px; margin-right: 5px;"></i>Create
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item" href="classwork_quiz.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-clipboard" style="color: green; margin-right: 10px;"></i>
                      Quiz
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_assignment.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-journal-text" style="color: green; margin-right: 10px;"></i>
                      Assignment
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_question.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-question-square" style="color: green; margin-right: 10px;"></i>
                      Question
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_material.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-journal-text" style="color: green; margin-right: 10px;"></i>
                      Material
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="classwork_exam.php?class_id=<?php echo $class_id; ?>">
                      <i class="bi bi-clipboard" style="color: green; margin-right: 10px;"></i>
                      Exam
                    </a>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <a class="nav-link dropdown-item" data-toggle="modal" data-target="#myModal"
                      style="cursor: pointer;">
                      <i class="bi bi-card-list" style="color: green; margin-right: 10px; margin-left: 10px;"></i>
                      Topic
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="row">
            <?php
            $_SESSION['class_topic'] = "";

            $sql_material = "SELECT material_id, title FROM classwork_material WHERE class_topic=? AND class_id=?";
            $stmt_titles_material = $db->prepare($sql_material);
            $stmt_titles_material->execute([$_SESSION['class_topic'], $class_id]);

            $sql_assignment = "SELECT assignment_id, title FROM classwork_assignment WHERE class_topic=? AND class_id=?";
            $stmt_titles_assignment = $db->prepare($sql_assignment);
            $stmt_titles_assignment->execute([$_SESSION['class_topic'], $class_id]);

            $sql_question = "SELECT question_id, title FROM classwork_question WHERE class_topic=? AND class_id=?";
            $stmt_titles_question = $db->prepare($sql_question);
            $stmt_titles_question->execute([$_SESSION['class_topic'], $class_id]);

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
              <div class="material mb-3" href="">
                <div class="col-12 grid-margin strech-card" style="height: 4vh;">
                  <div class="card mb-3" style="border-radius: 0px; height: 10vh; width=100%;">
                    <div class="material-content"
                      style="display: flex; justify-content: space-between; align-items: center;">
                      <div class="material-body">
                        <div style="display: flex; align-items: center;">
                          <div
                            style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px; margin-top: 12px;">
                            <i class="bi bi-journal-text" style="color: white; line-height: 41px; font-size: 26px;"></i>
                          </div>
                          <p style="margin-top: 23px; margin-left: 30px; font-size: 17px;">
                            <?php echo $truncatedTitle ?>
                          </p>
                        </div>
                      </div>
                      <div class="dropdown mt-3">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                          style="font-size: 20px; color: green; margin-right: 10px;">
                          <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item"
                              href="edit_material.php?updateid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                          </li>
                          <li>
                            <a class="dropdown-item"
                              href="delete_material.php?deleteid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                          </li>
                        </ul>
                      </div>
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
              <div class="assignment mb-3" href="">
                <div class="col-12 grid-margin strech-card" style="height: 4vh;">
                  <div class="card mb-3" style="border-radius: 0px; height: 10vh; width=100%;">
                    <div class="assignment-content"
                      style="display: flex; justify-content: space-between; align-items: center;">
                      <div class="assignment-body">
                        <div style="display: flex; align-items: center;">
                          <div
                            style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px; margin-top: 12px;">
                            <i class="bi bi-journal-text" style="color: white; line-height: 41px; font-size: 26px;"></i>
                          </div>
                          <p style="margin-top: 23px; margin-left: 30px; font-size: 17px;">
                            <?php echo $truncatedTitle ?>
                          </p>
                        </div>
                      </div>
                      <div class="dropdown mt-3">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                          style="font-size: 20px; color: green; margin-right: 10px;">
                          <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item"
                              href="edit_assignment.php?updateid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                          </li>
                          <li>
                            <a class="dropdown-item"
                              href="delete_assignment.php?deleteid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                          </li>
                        </ul>
                      </div>
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
              $truncatedQuestion = implode(' ', array_slice($words, 0, $maxWords));

              if (count($words) > $maxWords) {
                $truncatedTitle .= '...';
              }
              ?>
              <div class="question mb-4" href="">
                <div class="col-12 grid-margin strech-card" style="height: 4vh;">
                  <div class="card mb-3" style="border-radius: 0px; height: 10vh; width=100%;">
                    <div class="question-content"
                      style="display: flex; justify-content: space-between; align-items: center;">
                      <div class="question-body">
                        <div style="display: flex; align-items: center;">
                          <div
                            style="display: inline-block; background-color: green; border-radius: 50%; width: 40px; height: 40px; text-align: center; margin-left: 20px; margin-top: 12px;">
                            <i class="bi bi-question-square"
                              style="color: white; line-height: 41px; font-size: 26px;"></i>
                          </div>
                          <p style="margin-top: 23px; margin-left: 30px; font-size: 17px;">
                            <?php echo $truncatedTitle ?>
                          </p>
                        </div>
                      </div>
                      <div class="dropdown mt-3">
                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                          style="font-size: 20px; color: green; margin-right: 10px;">
                          <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item"
                              href="edit_question.php?updateid=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                          </li>
                          <li>
                            <a class="dropdown-item"
                              href="delete_question.php?deleteid=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
          <div class="row">
            <?php
            include("db_conn.php");
            $sql = "SELECT class_topic FROM topic WHERE teacher_id=? AND class_id=$class_id";
            $stmt = $db->prepare($sql);
            $stmt->execute([$teacher_id]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $_SESSION['class_topic'] = $row['class_topic'];
              ?>
              <div class="col-12 grid-margin stretch-card">
                <div class="card" style="border-radius: 0px;">
                  <div class="topic" href="#">
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
                            <a class="dropdown-item" data-toggle="modal" data-target="#editMyModal"
                              style="cursor: pointer;">Delete</a>
                          </li>
                        </ul>
                        <div class="modal fade" id="editMyModal">
                          <form action="" method="post" class="forms-sample" id="myEditForm">
                            <div class="modal-dialog modal-dialog-centered" style="top: -6vh;">
                              <div class="modal-content">
                                <div class="modal-body">
                                  <h4 class="mb-4">Rename Class Topic</h4>
                                  <div id="editedValidationAlert" class="alert alert-danger" style="display: none;">Class Topic cannot be
                                    empty.</div>
                                  <div class="form-floating mb-2">
                                    <input type="text" name="rename_topic" id="rename_topic" class="form-control" id="floatingInput"
                                      placeholder="Class Name">
                                    <label for="floatingName">Class Topic</label>
                                  </div>
                                </div>
                                <div class="modal-footer" style="border: none;">
                                  <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                  <button type="submit" name="submit_topic" class="btn btn-success">Add</button>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <p class="bottom-border mb-4"></p>
                    <div class="topic-body">
                      <?php
                      $sql_material = "SELECT material_id, title FROM classwork_material WHERE class_topic=? AND class_id=?";
                      $stmt_titles_material = $db->prepare($sql_material);
                      $stmt_titles_material->execute([$_SESSION['class_topic'], $class_id]);

                      $sql_assignment = "SELECT assignment_id, title FROM classwork_assignment WHERE class_topic=? AND class_id=?";
                      $stmt_titles_assignment = $db->prepare($sql_assignment);
                      $stmt_titles_assignment->execute([$_SESSION['class_topic'], $class_id]);

                      $sql_question = "SELECT question_id, title FROM classwork_question WHERE class_topic=? AND class_id=?";
                      $stmt_titles_question = $db->prepare($sql_question);
                      $stmt_titles_question->execute([$_SESSION['class_topic'], $class_id]);

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
                                    <a class="dropdown-item"
                                      href="edit_material.php?updateid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_material.php?deleteid=<?php echo $material_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
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
                                    <a class="dropdown-item"
                                      href="edit_assignment.php?updateid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_assignment.php?deleteid=<?php echo $assignment_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
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
                                    <a class="dropdown-item"
                                      href="edit_question.php?updateid=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Edit</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item"
                                      href="delete_question.php?delete=<?php echo $question_id ?>&class_id=<?php echo $class_id ?>">Delete</a>
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
    var classTopicInput = document.getElementById('class_topic');
    var validationAlert = document.getElementById('validationAlert');

    form.addEventListener('submit', function (event) {
      // Check if class_topic field is empty
      if (classTopicInput.value.trim() === '') {
        event.preventDefault(); // Prevent form submission
        validationAlert.style.display = 'block'; // Show error message
        classTopicInput.classList.add('is-invalid'); // Add the is-invalid class to highlight the field
      }
    });

    // Add an event listener to hide the validation message when the user starts typing
    classTopicInput.addEventListener('input', function () {
      validationAlert.style.display = 'none'; // Hide the error message
      classTopicInput.classList.remove('is-invalid'); // Remove the is-invalid class
    });

    var form = document.getElementById('myEditForm');
    var editedClassTopic = document.getElementById('rename_topic');
    var editedValidationAlert = document.getElementById('editedValidationAlert');

    form.addEventListener('submit', function (event) {
      if (editedClassTopic.value.trim() === '') {
        event.preventDefault();
        editedValidationAlert.style.display = 'block';
        editedClassTopic.classList.add('is-invalid');
      }
    });

    editedClassTopic.addEventListener('input', function () {
      editedValidationAlert.style.display = 'none';
      editedClassTopic.classList.remove('is-invalid');
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