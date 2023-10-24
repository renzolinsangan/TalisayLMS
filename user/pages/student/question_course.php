<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_GET['user_id'];
$class_id = $_GET['class_id'];
$question_id = $_GET['question_id'];

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

    $sql_get_question_info = "SELECT title, question, instruction, point, date, due_date, time, link, file, youtube, question_status FROM classwork_question WHERE teacher_id=? AND question_id=?";
    $stmt_get_question_info = $db->prepare($sql_get_question_info);
    $stmt_get_question_info->execute([$teacher_id, $question_id]);
    $question_data = $stmt_get_question_info->fetch(PDO::FETCH_ASSOC);

    if ($question_data) {
      $title = $question_data['title'];
      $question = $question_data['question'];
      $instruction = $question_data['instruction'];
      $point = $question_data['point'];
      $date = $question_data['date'];
      $due_date = $question_data['due_date'];
      $formatted_due_date = date("F j", strtotime($due_date));
      $time = $question_data['time'];
      $link = $question_data['link'];
      $file = $question_data['file'];
      $youtube = $question_data['youtube'];
      $question_status = $question_data['question_status'];
    }
  }
}

$sql_questionCourseStatus = "SELECT question_course_status FROM student_question_course_answer 
WHERE class_id = ? AND question_id = ? AND user_id = ?";
$stmt_questionCourseStatus = $db->prepare($sql_questionCourseStatus);
$stmt_questionCourseStatus->execute([$class_id, $question_id, $user_id]);
$questionCourseStatus = $stmt_questionCourseStatus->fetchColumn();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/course_question.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>

  <nav class="navbar navbar-light fs-3 mb-5">
    <div class="d-flex align-items-center justify-content-between w-100">
      <div class="d-flex align-items-center">
        <button type="button" class="go-back" onclick="goToClasswork('<?php echo $class_id; ?>')"><i
            class="bi bi-arrow-bar-left"></i></button>
        <p class="name text-body-secondary" style="margin-top: 10px; font-size: 22px; pointer-events: none;">
          <?php echo $first_name . " " . $last_name ?>
        </p>
        <h3 class="greater" style="margin-left: 10px; margin-top: 5px; margin-right: 10px; pointer-events: none;">&gt;
        </h3>
        <p class="classname text-body-secondary" style="margin-top: 10px; font-size: 22px; pointer-events: none;">
          <?php echo $class_name ?>
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
              <i class="bi bi-question-square" style="color: white; line-height: 48px; font-size: 30px;"></i>
            </div>
            <div>
              <h2>
                <?php echo $title ?>
              </h2>
              <p class="text-body-secondary">
                <?php echo $first_name . " " . $last_name ?>
              </p>
              <p>
                <?php echo $point ?> points
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-11 col-sm-12">
          <p class="text-end text-body-secondary" style="margin-top: -40px;">Due
            <?php echo $formatted_due_date . ", " . $time ?>
          </p>
        </div>
        <div class="divider mb-3" id="divider"></div>
      </div>
      <div class="row justify-content-left align-items-center">
        <div class="col-md-9">
          <?php echo $instruction ?>
        </div>
        <div class="col-md-9 mt-5">
          <?php
          $questions = explode("\n", $question);

          foreach ($questions as $q) {
            echo "$q<br>";
          }
          ?>
        </div>
      </div>
      <div class="row justify-content-left align-items-center">
        <?php if (!empty($filePath) && !empty($file)) {
          ?>
          <div class="col-md-3 mt-5" style="margin-right: 15vh;">
            <div class="card">
              <a href="<?php echo $filePath; ?>" style="text-decoration: none; margin-left: 30px;">
                <div class="row mt-3" style="margin-bottom: -15px;">
                  <div class="col-md-9">
                    <p
                      style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                      <?php echo $file ?>
                    </p>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-9 text-body-secondary">
                    <?php echo strtoupper(pathinfo($file, PATHINFO_EXTENSION)); ?>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php
        }
        ?>
        <?php if (!empty($link) && $link != 'null') {
          ?>
          <div class="col-md-3 mt-5" style="margin-right: 15vh;">
            <div class="card">
              <a href="<?php echo $link ?>" target="_blank" style="text-decoration: none; margin-left: 30px;">
                <div class="row mt-3" style="margin-bottom: -15px;">
                  <div class="col-md-9">
                    <p
                      style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                      <?php echo $link ?>
                    </p>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-9 text-body-secondary">
                    LINK
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php
        }
        ?>
        <?php if (!empty($youtube) && $youtube != 'null') { ?>
          <div class="col-md-3 mt-5">
            <div class="card">
              <a href="<?php echo $youtube ?>" target="_blank" style="text-decoration: none; margin-left: 30px;">
                <div class="row mt-3" style="margin-bottom: -15px;">
                  <div class="col-md-9">
                    <p
                      style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                      <?php echo $youtube ?>
                    </p>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-9 text-body-secondary">
                    YOUTUBE LINK
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
      <?php
      if (isset($_POST['question_submit'])) {
        $question_answer = $_POST['question_answer'];
        $new_status = ($question_status === "missing") ? "turned-in late" : "turned in";

        $sql = "INSERT INTO student_question_course_answer (question_id, question_answer, user_id, class_id, teacher_id, 
        point, title, date, question_course_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtinsert = $db->prepare($sql);
        $result = $stmtinsert->execute([$question_id, $question_answer, $user_id, $class_id, $teacher_id, 
        $point, $title, $date, $new_status]);
      }

      $sql = "SELECT question_answer FROM student_question_course_answer WHERE class_id=? AND question_id=?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$class_id, $question_id]);
      $question_answer_data = $stmt->fetch(PDO::FETCH_ASSOC);
      $textAreaDisabled = $question_answer_data ? 'disabled' : '';
      ?>
      <form class="question" action="" method="post">
        <div class="row justify-content-left align-items-center mt-5 mb-5">
          <div class="col-md-9">
            <div class="card answer-box mb-5">
              <div class="card-body">
                <div class="card-header d-flex justify-content-between" style="border: none; background-color: white;">
                  <span>Your Answer</span>
                  <span class="text-body-secondary ml-auto">
                    <?php
                      if (!empty($questionCourseStatus)) {
                        echo ucfirst($questionCourseStatus);
                      } else {
                        echo ucfirst($question_status);
                      }
                      ?>
                  </span>
                </div>
                <div style="margin: 10px;">
                  <textarea name="question_answer" class="form-control auto-resize" id="floatingInput" <?php echo $textAreaDisabled ?>><?php echo $question_answer_data['question_answer'] ?? ''; ?></textarea>
                </div>
                <div class="text-end mt-4" style="margin-right: 12px;">
                  <button name="question_edit" class="btn btn-outline-secondary" type="button"
                    style="<?php echo $question_answer_data ? '' : 'display: none;' ?>">Edit Answer</button>
                  <button name="question_submit" class="btn btn-outline-secondary" type="submit"
                    style="<?php echo $question_answer_data ? 'display: none;' : '' ?>">Submit</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
      <?php
      if (isset($_POST['submit_editQuestion'])) {
        $edited_answer = $_POST['question_answer'];

        $sql = "UPDATE student_question_course_answer SET question_answer = ? WHERE class_id = ? AND question_id = ?";
        $stmtupdate = $db->prepare($sql);
        $result = $stmtupdate->execute([$edited_answer, $class_id, $question_id]);
      }
      ?>
      <form class="edited_question" action="" method="post" style="display: none;">
        <div class="row justify-content-left align-items-center mt-5 mb-5">
          <div class="col-md-9">
            <div class="card answer-box mb-5">
              <div class="card-body">
                <div class="card-header d-flex justify-content-between" style="border: none; background-color: white;">
                  <span>Your Answer</span>
                  <span class="text-body-secondary ml-auto">
                    <?php
                      if (!empty($questionCourseStatus)) {
                        echo ucfirst($questionCourseStatus);
                      } else {
                        echo ucfirst($question_status);
                      }
                      ?>
                  </span>
                </div>
                <div style="margin: 10px;">
                  <textarea name="question_answer" class="form-control auto-resize"
                    id="floatingInput"><?php echo $question_answer_data['question_answer'] ?? ''; ?></textarea>
                </div>
                <div class="text-end mt-4" style="margin-right: 12px;">
                  <button name="submit_editQuestion" class="btn btn-outline-secondary" type="submit">Submit</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    function goToClasswork(classId) {
      window.location.href = `class_course.php?class_id=${classId}`;
    }

    const textareas = document.querySelectorAll(".auto-resize");

    textareas.forEach((textarea) => {
      const initialHeight = textarea.scrollHeight + "px";

      textarea.addEventListener("input", function () {
        this.style.height = initialHeight;
        this.style.height = (this.scrollHeight <= this.clientHeight) ? initialHeight : this.scrollHeight + "px";
      });
    });

    const textarea = document.querySelector(".auto-resize");
    const submittedAnswerContainer = document.getElementById("submittedAnswerContainer");
    const submittedAnswerTextarea = submittedAnswerContainer.querySelector("textarea");

    textarea.addEventListener("input", function () {
      this.style.height = "auto";
      this.style.height = this.scrollHeight + "px";
    });

    textarea.dispatchEvent(new Event("input"));
  </script>
  <script>
    document.querySelector('button[name="question_edit"]').addEventListener('click', function () {
      document.querySelector('.question').style.display = 'none';
      document.querySelector('.edited_question').style.display = 'block';
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