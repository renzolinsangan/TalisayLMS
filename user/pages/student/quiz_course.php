<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'];
$quiz_id = $_GET['quiz_id'];

$sql_get_teacher_id = "SELECT teacher_id FROM class_enrolled WHERE class_id=?";
$stmt_get_teacher_id = $db->prepare($sql_get_teacher_id);
$stmt_get_teacher_id->execute([$class_id]);
$teacher_id = $stmt_get_teacher_id->fetchColumn();

if ($teacher_id) {
  $sql_get_class_info = "SELECT class_name, first_name, last_name FROM class_enrolled WHERE teacher_id=?";
  $stmt_get_class_info = $db->prepare($sql_get_class_info);
  $stmt_get_class_info->execute([$teacher_id]);
  $class_info = $stmt_get_class_info->fetch(PDO::FETCH_ASSOC);

  if ($class_info) {
    $class_name = $class_info['class_name'];
    $first_name = $class_info['first_name'];
    $last_name = $class_info['last_name'];

    $sql_get_quiz_info = "SELECT quizTitle, quizInstruction, quizLink, quizPoint, date, dueDate, time, quizStatus 
    FROM classwork_quiz WHERE teacher_id=? AND quiz_id=?";
    $stmt_get_quiz_info = $db->prepare($sql_get_quiz_info);
    $stmt_get_quiz_info->execute([$teacher_id, $quiz_id]);
    $quiz_data = $stmt_get_quiz_info->fetch(PDO::FETCH_ASSOC);

    if ($quiz_data) {
      $quizTitle = $quiz_data['quizTitle'];
      $quizInstruction = $quiz_data['quizInstruction'];
      $quizLink = $quiz_data['quizLink'];
      $quizPoint = $quiz_data['quizPoint'];
      $date = $quiz_data['date'];
      $dueDate = $quiz_data['dueDate'];
      $formatted_due_date = date("F j", strtotime($dueDate));
      $time = $quiz_data['time'];
      $quizStatus = $quiz_data['quizStatus'];
    }
  }
}

$sql_quizCourseStatus = "SELECT quiz_course_status FROM student_quiz_course_answer 
WHERE class_id = ? AND quiz_id = ? AND user_id = ?";
$stmt_quizCourseStatus = $db->prepare($sql_quizCourseStatus);
$stmt_quizCourseStatus->execute([$class_id, $quiz_id, $user_id]);
$quizCourseStatus = $stmt_quizCourseStatus->fetchColumn();

$sqlQuizScore = "SELECT score FROM quizgrade 
WHERE quizTitle = ? AND quiz_id = ? AND student_id = ?";
$stmtQuizScore = $db->prepare($sqlQuizScore);
$stmtQuizScore->execute([$quizTitle, $quiz_id, $user_id]);
$quizScore = $stmtQuizScore->fetchColumn();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/quiz_course.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>

  <nav class="navbar navbar fs-3 mb-5" style="background-color: green;">
    <div class="d-flex align-items-center justify-content-between w-100">
      <div class="d-flex align-items-center" style="margin-top: -3px;">
        <button type="button" class="go-back" onclick="goToClasswork('<?php echo $class_id; ?>')"><i
            class="bi bi-arrow-bar-left" style="color: white;"></i></button>
        <p class="name" style="margin-top: 6px; font-size: 22px; pointer-events: none; color: white;">
          Quiz
        </p>
      </div>
    </div>
  </nav>

  <div class="wrapper">
    <div class="container">
      <div class="row justify-content-left align-items-center">
        <div class="col col-sm-12">
          <div class="d-flex align-items-center justify-content-left">
            <div
              style="display: inline-block; background-color: green; border-radius: 50%; width: 48px; height: 48px; text-align: center; margin-right: 10px; margin-bottom: 70px;">
              <i class="bi bi-card-list" style="color: white; line-height: 48px; font-size: 30px;"></i>
            </div>
            <div>
              <h2>
                <?php echo $quizTitle ?>
              </h2>
              <p class="text-body-secondary">
                <?php echo $first_name . " " . $last_name ?>
              </p>
              <?php
              if ($quizScore !== false) {
                $score = $quizScore;
                ?>
                <p>
                  <?php echo $score ?> /
                  <?php echo $quizPoint ?>
                </p>
                <?php
              } else {
                ?>
                <p>
                  <?php echo $quizPoint ?> points
                </p>
                <?php
              }
              ?>
            </div>
          </div>
        </div>
        <div class="col-md-8 col-sm-12">
          <p class="text-end text-body-secondary" style="margin-top: -40px;">Due
            <?php echo $formatted_due_date . ", " . $time ?>
          </p>
        </div>
        <div class="divider mb-3" id="divider"></div>
      </div>
      <div class="row justify-content-left align-items-center">
        <div class="col-md-7">
          <?php echo $quizInstruction ?>
        </div>
      </div>
      <?php
      include("config.php");

      if (isset($_POST['mark_done'])) {
        $new_status = ($quizStatus === "missing") ? "turned-in late" : "turned in";

        $sqlQuiz = "INSERT INTO student_quiz_course_answer (quiz_id, quizTitle, quizLink, quizPoint, date, user_id, class_id,
        teacher_id, quiz_course_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtQuiz = $db->prepare($sqlQuiz);
        $stmtQuiz->execute([$quiz_id, $quizTitle, $quizLink, $quizPoint, $date, $user_id, $class_id, $teacher_id, $new_status]);
      }
      ?>
      <form class="assignment" action="" method="post">
        <div class="row justify-content-left align-items-center mb-5" id="submit-card">
          <div class="col-md-8"></div>
          <div class="col-md-4 mt-5">
            <div class="card">
              <div class="row justify-content-between">
                <div class="col mt-4" style="margin-left: 25px;">
                  <h5>Your Work</h5>
                </div>
                <div class="col text-end mt-4" style="margin-right: 25px;">
                  <p class="text-body-secondary">
                    <?php
                    if (!empty($quizCourseStatus)) {
                      echo ucfirst($quizCourseStatus);
                    } else {
                      echo ucfirst($quizStatus);
                    }
                    ?>
                  </p>
                </div>
              </div>
              <div class="row justify-content-center mb-2">
                <div class="col-md-12 text-center">
                  <p class="text-body-secondary">Quiz Link:</p>
                  <a href="<?php echo $quizLink ?>" target="_blank">
                    <?php echo $quizLink ?>
                  </a>
                  <p class="text-body-secondary mt-3">make sure to open with correct google account in your browser.</p>
                </div>
              </div>
              <div class="row justify-content-center align-items-center mb-5">
                <div class="d-grid gap-2 col-11 mx-auto">
                  <?php if ($quizCourseStatus == 'turned in' || $quizCourseStatus == 'turned-in late'): ?>
                    <button class="btn btn-success" id="unsubmitButton" name="unsubmit" type="submit">
                      Unsubmit</button>
                  <?php else: ?>
                    <button class="btn btn-success" id="turnInButton" name="mark_done" type="submit">Mark as Done</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="col mt-2 text-center">
          <p>
            <?php echo $class_name ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <script>
    function goToClasswork(classId) {
      window.location.href = `class_course.php?class_id=${classId}`;
    }
  </script>
  <script>
    document.querySelector('button[name="unsubmit"]').addEventListener('click', function () {
      document.querySelector('.assignment').style.display = 'none';
      document.querySelector('.edited_assignment').style.display = 'block';
    });
  </script>
  <script type="text/javascript" src="js/virtual-select.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
    crossorigin="anonymous"></script>
</body>

</html>