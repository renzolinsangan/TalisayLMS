<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'];
$exam_id = $_GET['exam_id'];

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

    $sql_get_exam_info = "SELECT examTitle, examInstruction, examPoint, date, dueDate, time, examStatus 
    FROM classwork_exam WHERE teacher_id=? AND exam_id=?";
    $stmt_get_exam_info = $db->prepare($sql_get_exam_info);
    $stmt_get_exam_info->execute([$teacher_id, $exam_id]);
    $exam_data = $stmt_get_exam_info->fetch(PDO::FETCH_ASSOC);

    if ($exam_data) {
      $examTitle = $exam_data['examTitle'];
      $examInstruction = $exam_data['examInstruction'];
      $examPoint = $exam_data['examPoint'];
      $date = $exam_data['date'];
      $dueDate = $exam_data['dueDate'];
      $formatted_due_date = date("F j", strtotime($dueDate));
      $time = $exam_data['time'];
      $examStatus = $exam_data['examStatus'];
    }
  }
}

$sql_examCourseStatus = "SELECT exam_course_status FROM student_exam_course_answer 
WHERE class_id = ? AND exam_id = ? AND user_id = ?";
$stmt_examCourseStatus = $db->prepare($sql_examCourseStatus);
$stmt_examCourseStatus->execute([$class_id, $exam_id, $user_id]);
$examCourseStatus = $stmt_examCourseStatus->fetchColumn();

$sqlExamScore = "SELECT score FROM examgrade 
WHERE examTitle = ? AND exam_id = ? AND student_id = ?";
$stmtExamScore = $db->prepare($sqlExamScore);
$stmtExamScore->execute([$examTitle, $exam_id, $user_id]);
$examScore = $stmtExamScore->fetchColumn();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/exam_course.css">
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
          Exam
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
                <?php echo $examTitle ?>
              </h2>
              <p class="text-body-secondary">
                <?php echo $first_name . " " . $last_name ?>
              </p>
              <?php
              if ($examScore !== false) {
                $score = $examScore;
                ?>
                <p>
                  <?php echo $score ?> /
                  <?php echo $examPoint ?>
                </p>
                <?php
              } else {
                ?>
                <p>
                  <?php echo $examPoint ?> points
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
          <?php echo $examInstruction ?>
        </div>
      </div>
      <?php
      include("config.php");

      if (isset($_POST['mark_done'])) {
        $new_status = ($examStatus === "missing") ? "turned-in late" : "turned in";

        $sqlExam = "INSERT INTO student_exam_course_answer (exam_id, examTitle, examPoint, date, user_id, class_id,
        teacher_id, exam_course_status) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
        $stmtExam = $db->prepare($sqlExam);
        $stmtExam->execute([$exam_id, $examTitle, $examPoint, $user_id, $class_id, $teacher_id, $new_status]);

        header("Location:exam_course.php?class_id=$class_id&exam_id=$exam_id&user_id=$user_id");
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
                    if (!empty($examCourseStatus)) {
                      echo ucfirst($examCourseStatus);
                    } else {
                      echo ucfirst($examStatus);
                    }
                    ?>
                  </p>
                </div>
              </div>
              <div class="row justify-content-center mb-2">
                <div class="col-md-12 text-center">
                  <p class="text-body-secondary mt-3">
                  Once you are done with your face-to-face exam, you can now turn 
                  in so that the teacher can insert your score.</p>
                </div>
              </div>
              <div class="row justify-content-center align-items-center mb-5">
                <div class="d-grid gap-2 col-11 mx-auto">
                  <?php if ($examCourseStatus == 'turned in' || $examCourseStatus == 'turned-in late'): ?>
                    <button class="btn btn-success" type="button">
                      Exam Submitted</button>
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