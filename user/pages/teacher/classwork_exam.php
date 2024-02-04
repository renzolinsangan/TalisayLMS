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

if (isset($_POST['createQuestion'])) {
  $question = $_POST['question'];
  $questionType = $_POST['questionType'];
  $questionPoint = $_POST['questionPoint'];
  $questionChoices = $_POST['choices'];
  $questionAnswerKey = ""; 

  switch ($questionType) {
    case 'multiple':
      $questionAnswerKey = $_POST['multipleChoiceAnswerKey'];
      break;
    case 'truefalse':
      $questionAnswerKey = $_POST['trueFalseAnswerKey'];
      break;
    case 'identification':
      $questionAnswerKey = $_POST['identificationAnswerKey'];
      break;
  }
  $questionStatus = "posted";
  $teacher_id = $_SESSION['user_id'];
  $class_id = $_GET['class_id'];

  $serializedChoices = serialize($questionChoices);

  $sqlCreateQuestion = "INSERT INTO classwork_examquestion (question, questionType, questionPoint, questionChoices, 
  questionAnswerKey, questionStatus, teacher_id, class_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $stmtCreateQuestion = $db->prepare($sqlCreateQuestion);
  $stmtCreateQuestion->execute([
    $question,
    $questionType,
    $questionPoint,
    $serializedChoices,
    $questionAnswerKey,
    $questionStatus,
    $teacher_id,
    $class_id
  ]);

  header("Location:classwork_exam.php?class_id=$class_id");
}

if (isset($_POST['setQuiz'])) {
  $sqlSelectQuestion = "SELECT examQuestion_id, questionPoint FROM classwork_examquestion 
  WHERE class_id = ? AND teacher_id = ? and questionStatus = 'posted'";
  $stmtSelectQuestion = $db->prepare($sqlSelectQuestion);
  $stmtSelectQuestion->execute([$class_id, $teacher_id]);
  $questionResult = $stmtSelectQuestion->fetchAll(PDO::FETCH_ASSOC);

  $examQuestionIds = array_column($questionResult, 'examQuestion_id');
  $questionIdsJson = json_encode($examQuestionIds);
  $questionPoints = array_column($questionResult, 'questionPoint');

  $totalPoints = array_sum($questionPoints);

  $examTitle = $_POST['examTitle'];
  $examInstruction = $_POST['examInstruction'];
  $due_date = $_POST['due_date'];
  $class_topic = $_POST['class_topic'];
  $teacher_id = $_SESSION['user_id'];

  $sqlQuizCreate = "INSERT INTO classwork_exam (examTitle, examInstruction, questionIds, totalPoint, 
  date, due_date, class_topic, exam_status, class_id, teacher_id) VALUES (?, ?, ?, ?, NOW(), ?, ?, 'assigned',?, ?)";
  $stmtQuizCreate = $db->prepare($sqlQuizCreate);
  $stmtQuizCreate->execute([$examTitle, $examInstruction, $questionIdsJson, $totalPoints, $due_date, $class_topic, $class_id, $teacher_id]);

  $sqlUpdateStatus = "UPDATE classwork_examquestion SET questionStatus = 'submitted' WHERE examQuestion_id IN (" . implode(',', $examQuestionIds) . ")";
  $stmtUpdateStatus = $db->prepare($sqlUpdateStatus);
  $stmtUpdateStatus->execute();

  header("Location: class_classwork.php?class_id=$class_id");
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/classwork_exam.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>
  <form action="" method="post" id="createExamForm" style="width: 100%;">
    <nav class="navbar navbar-light fs-3 mb-5">
      <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
          <button type="button" class="go-back" onclick="goToClasswork('<?php echo $class_id; ?>')"><i
              class="bi bi-x-lg custom-icon"></i></button>
          <p class="text-body-secondary" style="margin-top: 10px; font-size: 22px;">Exam</p>
        </div>
        <div>
          <div class="btn-group">
            <button type="submit" id="setQuiz" name="setQuiz" class="btn btn-success"
              style="margin-right: 3px; width: 15vh; margin-bottom: 20px;">Create</button>
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
        <div class="row">
          <div class="col-md-12">
            <div class="card" style="padding: 30px; border-top: 20px solid green;">
              <div class="col-md-4">
                <input type="text" class="form-control" id="quizTitle" name="examTitle" placeholder="Exam title"
                style="border-radius: 0;">
              </div>
              <div class="col-md-9 mt-3">
                <textarea type="text" class="form-control auto-resize" id="quizInstruction" name="examInstruction"
                  placeholder="Exam instruction" style="border-radius: 0;"></textarea>
              </div>
              <div class="row mt-4">
                <div class="col-md-4">
                  <label>Due-Date</label>
                  <input type="date" name="due_date" class="form-control" min="<?php echo date('Y-m-d'); ?>"
                  style="border: none; border-bottom: 1px solid #ccc; outline: none; border-radius: 0;">
                </div>
                <div class="col-md-4">
                  <label>For</label>
                  <select id="classTopicSelect" name="class_topic" class="form-select" data-search="true"
                  data-silent-initial-value-set="true" style="border: none; border-bottom: 1px solid #ccc; outline: none; border-radius: 0;">
                  <option value="" selected>No Topic</option>
                  <?php
                  include("db_conn.php");
                  $teacher_id = $_SESSION['user_id'];

                  $sql = "SELECT class_topic FROM topic WHERE teacher_id=$teacher_id AND class_id=$class_id";
                  $result = mysqli_query($conn, $sql);

                  while ($row = mysqli_fetch_assoc($result)) {
                    $class_topic = $row['class_topic'];
                    echo '<option value="' . $class_topic . '">' . $class_topic . '</option>';
                  }
                  ?>
                </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mt-3">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop">+ Add
              Question</button>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col">
            <div class="card">
              <div class="card-header" style="border-bottom: none; margin-top: 10px; margin-bottom: -5px;">
                <h2 style="color: green;">Questions Created</h2>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Question</th>
                      <th scope="col">Answer Key</th>
                      <th scope="col">Type</th>
                      <th scope="col">Point</th>
                      <th scope="col">Choices</th>
                    </tr>
                  </thead>
                  <?php
                  include("config.php");

                  $sqlQuestionsCreated = "SELECT * FROM classwork_examquestion
                  WHERE class_id = ? AND teacher_id = ? AND questionStatus = 'posted'";
                  $stmtQuestionsCreated = $db->prepare($sqlQuestionsCreated);
                  $stmtQuestionsCreated->execute([$class_id, $teacher_id]);
                  $questionsCreatedResult = $stmtQuestionsCreated->fetchAll(PDO::FETCH_ASSOC);

                  if (!empty($questionsCreatedResult)) {
                    foreach ($questionsCreatedResult as $questionInfo) {
                      $question = $questionInfo['question'];
                      $answerKey = $questionInfo['questionAnswerKey'];
                      $questionType = $questionInfo['questionType'];
                      $questionPoint = $questionInfo['questionPoint'];
                      $questionChoices = unserialize($questionInfo['questionChoices']);

                      ?>
                      <tr>
                        <td>
                          <?php echo $question; ?>
                        </td>
                        <td>
                          <?php echo $answerKey; ?>
                        </td>
                        <td>
                          <?php echo $questionType; ?>
                        </td>
                        <td>
                          <?php echo $questionPoint; ?>
                        </td>
                        <td>
                          <?php
                          echo implode(', ', $questionChoices);
                          ?>
                        </td>
                      </tr>
                      <?php
                    }
                  }
                  ?>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <form action="" method="post">
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="border-bottom: none;">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="col-md-8 mb-3">
              <h3 class="mb-3">Question:</h3>
              <input type="text" class="form-control" name="question" placeholder="Type your question here"
                style="border:none; border-bottom: 1px solid #ccc; border-radius: 0;">
            </div>
            <div class="col mb-3">
              <div class="d-flex justify-content-between">
                <div class="col-md-4">
                  <select name="questionType" id="questionType" class="form-select mt-3">
                  <option value="multiple">Multiple Choice</option>
                    <option value="truefalse">True or False</option>
                    <option value="identification">Identification</option>
                  </select>
                </div>
                <div class="col-md-2 mt-3 d-flex align-items-center">
                  <span>Point</span>
                  <input type="text" name="questionPoint" class="form-control"
                    style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;">
                </div>
              </div>
            </div>
            <div id="multipleDiv">
              <div class="col-md-5 d-flex justify-content-between" style="margin-left: 10px;">
                <div class="form-check mb-2 d-flex align-items-center justify-content-between">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                  <input class="form-control" type="text" name="choices[1]" placeholder="Choice 1" id="inputField1"
                    style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;">
                </div>
              </div>
              <div class="col-md-5 d-flex justify-content-between" style="margin-left: 10px;">
                <div class="form-check mb-2 d-flex align-items-center justify-content-between">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                  <input class="form-control" type="text" name="choices[2]" placeholder="Choice 2" id="inputField2"
                    style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;">
                </div>
              </div>
              <div class="col-md-5 d-flex justify-content-between" style="margin-left: 10px;">
                <div class="form-check mb-2 d-flex align-items-center justify-content-between">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault3">
                  <input class="form-control" type="text" name="choices[3]" placeholder="Choice 3" id="inputField3"
                    style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;">
                </div>
              </div>
              <div class="col-md-5 d-flex justify-content-between" style="margin-left: 10px;">
                <div class="form-check mb-2 d-flex align-items-center justify-content-between">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault4">
                  <input class="form-control" type="text" name="choices[4]" placeholder="Choice 4" id="inputField4"
                    style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;">
                </div>
              </div>
              <div class="col-md-4 mt-3 d-flex align-items-center justify-content-between" style="margin-left: 5px;">
                <span><i class="bi bi-check-square-fill" style="color: green; font-size: 20px;"></i></span>
                <input type="text" class="form-control" name="multipleChoiceAnswerKey"
                  style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;"
                  placeholder="Answer Key">
              </div>
            </div>      
            <div id="truefalseDiv" style="display: none;">
              <div class="col-md-5 mb-3 d-flex justify-content-between" style="margin-left: 10px;">
                <div class="form-check mb-2 d-flex align-items-center justify-content-between">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1"
                    style="margin-right: 15px;">
                  <label>True</label>
                </div>
              </div>
              <div class="col-md-5 d-flex justify-content-between" style="margin-left: 10px;">
                <div class="form-check mb-2 d-flex align-items-center justify-content-between">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2"
                    style="margin-right: 15px;">
                  <label>False</label>
                </div>
              </div>
              <div class="col-md-4 mt-3 d-flex align-items-center justify-content-between" style="margin-left: 5px;">
                <span><i class="bi bi-check-square-fill" style="color: green; font-size: 20px;"></i></span>
                <input type="text" class="form-control" name="trueFalseAnswerKey"
                  style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;"
                  placeholder="Answer Key">
              </div>
            </div>
            <div id="identificationDiv" style="display: none;">
              <div class="col-md-4 mt-3 d-flex align-items-center justify-content-between" style="margin-left: 5px;">
                <span><i class="bi bi-check-square-fill" style="color: green; font-size: 20px;"></i></span>
                <input type="text" class="form-control" name="identificationAnswerKey"
                  style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; outline: none;"
                  placeholder="Answer Key">
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top: none;">
            <button type="submit" name="createQuestion" class="btn btn-success">Create Question</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script>
    document.getElementById('questionType').addEventListener('change', function () {
      var selectedOption = this.value;

      document.getElementById('multipleDiv').style.display = 'none';
      document.getElementById('truefalseDiv').style.display = 'none';
      document.getElementById('identificationDiv').style.display = 'none';

      document.getElementById(selectedOption + 'Div').style.display = 'block';
    });
  </script>
  <script>
    var form = document.getElementById('createExamForm');

    form.addEventListener('submit', function (event) {

      var examTitleInput = document.querySelector('input[name="examTitle"]');
      var examInstructionInput = document.querySelector('textarea[name="examInstruction"]');
      var examDueDateInput = document.querySelector('input[name="due_date"]');
      var examTopicInput = document.querySelector('select[name="class_topic"]');

      var isEmpty = false;

      if (examTitleInput.value === '' || examInstructionInput.value === '' || 
      examDueDateInput.value === '' || examTopicInput.value === '') {
        isEmpty = true;
      }

      if (examTitleInput.value.trim() === '') {
        isEmpty = true;
        examTitleInput.classList.add('is-invalid');
      } else {
        examTitleInput.classList.remove('is-invalid');
      }

      if (examInstructionInput.value.trim() === '') {
        isEmpty = true;
        examInstructionInput.classList.add('is-invalid');
      } else {
        examInstructionInput.classList.remove('is-invalid');
      }

      if (examDueDateInput.value.trim() === '') {
        isEmpty = true;
        examDueDateInput.classList.add('is-invalid');
      } else {
        examDueDateInput.classList.remove('is-invalid');
      }

      if (examTopicInput.value.trim() === '') {
        isEmpty = true;
        examTopicInput.classList.add('is-invalid');
      } else {
        examTopicInput.classList.remove('is-invalid');
      }

      if (isEmpty) {
        event.preventDefault();
      }
    })
  </script>
  <script type="text/javascript" src="js/virtual-select.min.js"></script>
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