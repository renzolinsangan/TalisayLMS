<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
}

include("config.php");
$teacher_id = $_SESSION['user_id'];

$_SESSION['id'] = $_GET['updateid'];
$id = $_SESSION['id'];

$sql = "SELECT * FROM classwork_exam WHERE exam_id = ? AND class_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id, $class_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$examTitle = $result['examTitle'];
$examInstruction = $result['examInstruction'];
$class_name = $result['class_name'];
$student = $result['student'];
$examPoint = $result['examPoint'];
$date = $result['date'];
$dueDate = $result['dueDate'];
$time = $result['time'];
$classTopic = $result['classTopic'];

if (isset($_POST['setExam'])) {
  $class_id = $_GET['class_id'];
  $teacher_id = $_SESSION['user_id'];
  $exam_id = $_SESSION['id'];
  $examTitle = $_POST['title'];
  $examInstruction = $_POST['instruction'];
  $class_name = $_POST['class_name'];
  $student = $_POST['student'];
  $examPoint = $_POST['point'];
  $date = date('Y-m-d');
  $dueDate = $_POST['due_date'];
  $time = $_POST['time'];
  $classTopic = $_POST['class_topic'];

  $sql = "UPDATE classwork_exam 
          SET examTitle = ?,
              examInstruction = ?,
              class_name = ?,
              student = ?,
              examPoint = ?,
              date = ?,
              dueDate = ?,
              time = ?,
              classTopic = ?
          WHERE teacher_id = ? 
          AND class_id = ? 
          AND exam_id = ?";
  $stmtupdate = $db->prepare($sql);

  if ($stmtupdate) {
    $stmtupdate->execute([
      $examTitle,
      $examInstruction,
      $class_name,
      $student,
      $examPoint,
      $date,
      $dueDate,
      $time,
      $classTopic,
      $teacher_id,
      $class_id,
      $exam_id
    ]);

    header("Location: class_classwork.php?class_id=$class_id");
    exit();
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/cw_question.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>

  <form action="" method="post" style="width: 100%;">
    <nav class="navbar navbar-light fs-3 mb-5">
      <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
          <button type="button" class="go-back" onclick="goToClasswork('<?php echo $class_id; ?>')"><i
              class="bi bi-x-lg custom-icon"></i></button>
          <p class="text-body-secondary" style="margin-top: 10px; font-size: 22px;">Exam</p>
        </div>
        <div>
          <div class="btn-group">
            <button type="submit" id="setExam" name="setExam" class="btn btn-success"
              style="margin-right: 3px; width: 15vh; margin-bottom: 10px;">Update</button>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
              data-bs-toggle="dropdown" aria-expanded="false"
              style="margin-right: 15px; width: 5vh; height: 38px; margin-bottom: 10px;">
              <span class="visually-hidden">Toggle Dropdown</span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end" style="margin-top:0; margin-bottom: 0;">
              <li><a class="dropdown-item" href="#">Save Draft</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="#">Discard Draft</a></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <div class="wrapper">
      <div class="container">
        <section class="row">
          <div class="col-md-7 mb-4">
            <div class="row" style="padding-bottom: 4vh">
              <div class="col-md-1" style="margin-left: 3px; margin-right: 5px;">
                <i class="bi bi-card-heading" style="font-size: 7vh;"></i>
              </div>
              <div class="col-md-7 mb-4" style="margin-left: 3px;">
                <div class="form-floating">
                  <textarea name="title" class="form-control auto-resize" id="floatingInput"
                    placeholder="Exam Title"><?php echo $examTitle ?></textarea>
                  <label for="floatingInput">Exam Title</label>
                </div>
              </div>
            </div>
            <div class="row" style="margin-top: -15px;">
              <div class="col-md-1" style="margin-left: 3px; margin-right: 5px;">
                <i class="bi bi-text-paragraph" style="font-size: 6vh;"></i>
              </div>
              <div class="col-md-10">
                <div class="form-floating">
                  <textarea name="instruction" class="form-control auto-resize" id="floatingInput"
                    placeholder="Instructions" style="height: 200px;"><?php echo $examInstruction ?></textarea>
                  <label for="floatingInput">Instructions</label>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-1 border-end"
            style="margin-top: -48px; margin-left: -20px; margin-right: 20px; height: 140vh;"></div>
          <div class="col-md-4" id="right-content">
            <div class="row">
              <label class="text-body-secondary mb-3" style="font-size: 20px;">For</label>
              <div class="col-md-10 mb-4">
                <?php
                include("db_conn.php");
                $teacher_id = $_SESSION['user_id'];

                $sql = "SELECT class_name FROM section WHERE teacher_id=$teacher_id AND class_id=$class_id";
                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                  $class_name = $row['class_name'];
                  echo '<input type="text" name="class_name" class="form-control" value="' . $class_name . '" readonly/>';
                }
                ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-10 mb-4">
                <select id="multipleSelect" multiple name="student" placeholder="Select Students" data-search="true"
                  data-silent-initial-value-set="true" style="height: 45px;">
                  <?php
                  include("db_conn.php");
                  $teacher_id = $_SESSION['user_id'];
                  $class_name = '';

                  $sql = "SELECT class_name FROM section WHERE teacher_id = $teacher_id AND class_id = $class_id";
                  $result = mysqli_query($conn, $sql);

                  while ($row = mysqli_fetch_assoc($result)) {
                    $class_name = $row['class_name'];
                  }

                  $sql_students = "SELECT student_firstname, student_lastname FROM class_enrolled WHERE teacher_id = $teacher_id AND class_name = '$class_name'";
                  $result_students = mysqli_query($conn, $sql_students);

                  while ($row_students = mysqli_fetch_assoc($result_students)) {
                    $student_firstname = $row_students['student_firstname'];
                    $student_lastname = $row_students['student_lastname'];
                    $selected = (preg_match('/^[A-ZÑ]/i', $student_firstname)) ? 'selected' : '';
                    echo '<option value="' . $student_firstname . ' ' . $student_lastname . '" ' . $selected . '>' . $student_firstname . ' ' . $student_lastname . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="row">
              <label class="text-body-secondary mb-3" style="font-size: 20px;">Points</label>
              <div class="col-md-6 mb-4">
                <input type="text" name="point" class="form-control" style="padding: 10px;" value="<?php echo $examPoint ?>">
              </div>
            </div>
            <div class="row">
              <label class="text-body-secondary mb-3" style="font-size: 20px;">Due-date</label>
              <div class="col-md-14 mb-4">
                <input type="date" name="due_date" id="due_date" class="form-control"
                  min="<?php echo date('Y-m-d'); ?>"  value="<?php echo $dueDate ?>">
              </div>
            </div>
            <div class="row">
              <label class="text-body-secondary mb-3" style="font-size: 20px;">Time (AM/PM)</label>
              <div class="col-md-6 mb-4">
                <input type="text" name="time" class="form-control" style="padding: 10px;" value="11:59 PM">
              </div>
            </div>
            <div class="row">
              <label class="text-body-secondary mb-3" style="font-size: 20px;">For</label>
              <div class="col-md-12 mb-4">
                <select id="classTopicSelect" name="class_topic" placeholder="Select Topics" data-search="true"
                  data-silent-initial-value-set="true" style="height: 45px;">
                  <option value="" selected>No Topic</option>
                  <?php
                  include("db_conn.php");
                  $teacher_id = $_SESSION['user_id'];

                  $sql = "SELECT class_topic FROM topic WHERE teacher_id=$teacher_id AND class_id=$class_id";
                  $result = mysqli_query($conn, $sql);
                  $matchingOptionFound = false;

                  while ($row = mysqli_fetch_assoc($result)) {
                    $class_topic = $row['class_topic'];

                    $sqlExamTopic = "SELECT classTopic FROM classwork_exam WHERE exam_id = $id AND class_id = $class_id";
                    $stmtResultExamTopic = mysqli_query($conn, $sqlExamTopic);

                    while ($rowExamTopic = mysqli_fetch_assoc($stmtResultExamTopic)) {
                      $classTopicExam = $rowExamTopic['classTopic'];
                      $selected = ($classTopicExam == $class_topic) ? 'selected' : '';

                      if ($selected) {
                        $matchingOptionFound = true;
                      }
                      echo '<option value="' . $class_topic . '" ' . $selected . '>' . $class_topic . '</option>';
                    }
                  }

                  if (!$matchingOptionFound) {
                    echo '<option value="" selected>No Topic</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </form>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var askButton = document.getElementById("setExam");
      var form = document.querySelector('form');

      askButton.addEventListener('click', function (event) {
        var examTitleInput = document.querySelector('[name="title"]');
        var instructionInput = document.querySelector('[name="instruction"]');
        var examLinkInput = document.querySelector('[name="link"]');
        var pointInput = document.querySelector('[name="point"]');
        var duedateInput = document.querySelector('[name="due_date"]');

        var isEmpty = false;

        if (examTitleInput.value.trim() === '') {
          isEmpty = true;
          examTitleInput.classList.add('is-invalid');
        } else {
          examTitleInput.classList.remove('is-invalid');
        }

        if (instructionInput.value.trim() === '') {
          isEmpty = true;
          instructionInput.classList.add('is-invalid');
        } else {
          instructionInput.classList.remove('is-invalid');
        }

        var googleFormsUrlPattern = /^https:\/\/(docs\.google\.com\/forms|forms\.gle)\/.*/;
        if (!googleFormsUrlPattern.test(examLinkInput.value)) {
          isEmpty = true;
          examLinkInput.classList.add('is-invalid');
        } else {
          examLinkInput.classList.remove('is-invalid');
        }


        var pointValue = pointInput.value.trim();
        if (pointValue === '' || isNaN(pointValue) || pointValue < 0 || pointValue > 100) {
          isEmpty = true;
          pointInput.classList.add('is-invalid');
        } else {
          pointInput.classList.remove('is-invalid');
        }

        if (pointValue.length > 3) {
          isEmpty = true;
          pointInput.classList.add('is-invalid');
        }

        if (duedateInput.value.trim() === '') {
          isEmpty = true;
          duedateInput.classList.add('is-invalid');
        } else {
          duedateInput.classList.remove('is-invalid');
        }

        if (isEmpty) {
          event.preventDefault();
        }
      });
    });
  </script>
  <script type="text/javascript" src="js/virtual-select.min.js"></script>
  <script>
    VirtualSelect.init({
      ele: '#multipleSelect'
    });

    VirtualSelect.init({
      ele: '#classTopicSelect'
    });
  </script>
  <script>
    function goToClasswork(classId) {
      window.location.href = `class_classwork.php?class_id=${classId}`;
    }

    const textarea = document.querySelector(".auto-resize");
    const initialHeight = textarea.scrollHeight + "px";

    textarea.addEventListener("input", function () {
      this.style.height = initialHeight;
      this.style.height = (this.scrollHeight <= this.clientHeight) ? initialHeight : this.scrollHeight + "px";
    });

    const textareas = document.querySelectorAll(".auto-resize");

    textareas.forEach((textarea) => {
      const initialHeight = textarea.scrollHeight + "px";

      textarea.addEventListener("input", function () {
        this.style.height = initialHeight;
        this.style.height = (this.scrollHeight <= this.clientHeight) ? initialHeight : this.scrollHeight + "px";
      });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
    crossorigin="anonymous"></script>
</body>

</html>