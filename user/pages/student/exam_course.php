<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'];
$tc_id = $_GET['tc_id'];
$exam_id = $_GET['exam_id'];
$teacher_id = $_GET['teacher_id'];

$sqlStudentName = "SELECT firstname, lastname FROM user_account WHERE user_id = ?";
$stmtStudentName = $db->prepare($sqlStudentName);
$stmtStudentName->execute([$user_id]);
$studentNameResult = $stmtStudentName->fetch(PDO::FETCH_ASSOC);

if ($studentNameResult) {
  $studentFirstName = $studentNameResult['firstname'];
  $studentLastName = $studentNameResult['lastname'];
}

$sqlExamInfo = "SELECT * FROM classwork_exam WHERE class_id = ? AND teacher_id = ? AND exam_id = ?";
$stmtExamInfo = $db->prepare($sqlExamInfo);
$stmtExamInfo->execute([$tc_id, $teacher_id, $exam_id]);
$examInfoResult = $stmtExamInfo->fetch(PDO::FETCH_ASSOC);

if ($examInfoResult) {
  $examTitle = $examInfoResult['examTitle'];
  $examInstruction = $examInfoResult['examInstruction'];
  $examStatus = $examInfoResult['exam_status'];
  $totalPoint = $examInfoResult['totalPoint'];
  $questionIds = $examInfoResult['questionIds'];

  $questionIdsArray = json_decode($questionIds, true);
  $questionIdsString = implode(',', $questionIdsArray);

  $sqlExamQuestions = "SELECT * FROM classwork_examquestion WHERE examQuestion_id IN ($questionIdsString)";
  $stmtExamQuestions = $db->prepare($sqlExamQuestions);
  $stmtExamQuestions->execute();
  $examQuestions = $stmtExamQuestions->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/courseQuiz.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>
  <nav class="navbar navbar-light fs-3 mb-5">
    <div class="d-flex align-items-center justify-content-between w-100">
      <div class="d-flex align-items-center">
        <button type="button" class="go-back" onclick="goBack()">
          <i class="bi bi-arrow-bar-left" style="color: white;"></i>
        </button>
        <p style="margin-top: 6px; font-size: 22px; pointer-events: none; color: white;">Exam</p>
      </div>
    </div>
  </nav>
  <?php
  if (isset($_POST['submitExam'])) {
    $multipleTotalScore = 0;
    $identificationTotalScore = 0;
    $trueFalseTotalScore = 0;

    foreach ($examQuestions as $question) {
      $questionType = $question['questionType'];
      $questionAnswerKey = $question['questionAnswerKey'];

      switch ($questionType) {
        case 'multiple':
          $selectedChoice = isset($_POST['flexRadioDefault_' . $question['examQuestion_id']]) ? $_POST['flexRadioDefault_' . $question['examQuestion_id']] : '';
          if (!empty($selectedChoice) && $selectedChoice == $questionAnswerKey) {
            $multipleTotalScore += $question['questionPoint'];
          }
          break;

        case 'identification':
          $userAnswer = isset($_POST['identificationAnswer_' . $question['examQuestion_id']]) ? $_POST['identificationAnswer_' . $question['examQuestion_id']] : '';
          if (!empty($userAnswer) && $userAnswer == $questionAnswerKey) {
            $identificationTotalScore += $question['questionPoint'];
          }
          break;

        case 'truefalse':
          $selectedValue = isset($_POST['trueFalseGroup_' . $question['examQuestion_id']]) ? $_POST['trueFalseGroup_' . $question['examQuestion_id']] : '';
          if (!empty($selectedValue) && $selectedValue == $questionAnswerKey) {
            $trueFalseTotalScore += $question['questionPoint'];
          }
          break;

      }
    }
    $totalScore = $multipleTotalScore + $identificationTotalScore + $trueFalseTotalScore;
    $examTitle = $_POST['examTitle'];
    $studentFirstName = $_POST['studentFirstName'];
    $studentLastName = $_POST['studentLastName'];
    $examPoint = $_POST['examPoint'];
    $examStatus = ($examStatus === 'assigned') ? 'turned-in' : 'turned-in late';
    $student_id = $_SESSION['user_id'];
    $teacher_id = $_GET['teacher_id'];
    $class_id = $_GET['class_id'];
    $exam_id = $_GET['exam_id'];

    $sqlExamSubmitted = "INSERT INTO examgrade (examTitle, studentFirstName, studentLastName, date, score, examPoint, 
    examStatus, student_id, teacher_id, class_id, exam_id) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
    $stmtExamSubmitted = $db->prepare($sqlExamSubmitted);
    $stmtExamSubmitted->execute([
      $examTitle,
      $studentFirstName,
      $studentLastName,
      $totalScore,
      $examPoint,
      $examStatus,
      $student_id,
      $teacher_id,
      $tc_id,
      $exam_id
    ]);
  }
  ?>
  <div class="wrapper">
    <?php
    $sqlExam = "SELECT * FROM examgrade WHERE student_id = ? AND teacher_id = ? AND class_id = ? AND exam_id = ?";
    $stmtExam = $db->prepare($sqlExam);
    $stmtExam->execute([$user_id, $teacher_id, $tc_id, $exam_id]);
    $examResult = $stmtExam->fetch(PDO::FETCH_ASSOC);

    if (empty($examResult)) {
      ?>
      <form action="" method="post">
        <div class="container">
          <div class="row mb-5 d-flex align-items-center justify-content-center">
            <div class="col-md-8">
              <div class="card" style="padding: 30px; border-top: 20px solid green;">
                <h2>
                  <?php echo $examTitle ?>
                  <input type="hidden" name="examTitle" value="<?php echo $examTitle ?>">
                </h2>
                <p class="text-body-secondary">
                  <?php echo $examInstruction ?>
                  <input type="hidden" name="examInstruction" value="<?php echo $examInstruction ?>">
                </p>
                <p>Total Points: <span>
                    <?php echo $totalPoint ?>
                    <input type="hidden" name="totalScore" value="<?php echo $totalScore ?>">
                    <input type="hidden" name="examPoint" value="<?php echo $totalPoint ?>">
                  </span></p>
                <p>Student Name: <span>
                    <?php echo $studentFirstName . ' ' . $studentLastName ?>
                  </span></p>
                <input type="hidden" name="studentFirstName" value="<?php echo $studentFirstName ?>">
                <input type="hidden" name="studentLastName" value="<?php echo $studentLastName ?>">
              </div>
            </div>
          </div>
          <?php
          $multipleChoiceQuestions = [];
          $identificationQuestions = [];
          $trueFalseQuestions = [];

          foreach ($examQuestions as $question) {
            $questionType = $question['questionType'];
            switch ($questionType) {
              case 'multiple':
                $multipleChoiceQuestions[] = $question;
                break;
              case 'identification':
                $identificationQuestions[] = $question;
                break;
              case 'truefalse':
                $trueFalseQuestions[] = $question;
                break;
            }
          }
          function displayQuestions($questions)
          {
            foreach ($questions as $question) {
              if ($question['questionType'] === 'multiple') {
                ?>
                <div class="row mb-5 d-flex align-items-center justify-content-center">
                  <div class="col-md-8">
                    <h2 style="color: green; margin-bottom: 10px;">Multiple Choice</h2>
                    <div class="card" style="padding: 30px;">
                      <div class="col mb-3 d-flex align-items-center justify-content-between">
                        <div class="col-md-10">
                        <h5>
                          <?php echo $question['question']; ?>
                        </h5>
                        </div>
                        <span style="color: green;">
                          <?php echo $question['questionPoint']; ?> point
                        </span>
                      </div>
                      <div class="col">
                        <?php
                        foreach (unserialize($question['questionChoices']) as $key => $value) {
                          ?>
                          <div class="form-check mb-3 d-flex align-items-center">
                            <input class="form-check-input" type="radio"
                              name="flexRadioDefault_<?php echo $question['examQuestion_id']; ?>"
                              id="flexRadioDefault_<?php echo $question['examQuestion_id'] . '_' . $key; ?>"
                              style="margin-right: 10px;" value="<?php echo $value ?>">
                            <label class="form-check-label"
                              for="flexRadioDefault<?php echo $question['examQuestion_id'] . '_' . $key; ?>">
                              <?php echo $value; ?>
                            </label>
                          </div>
                          <?php
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              } elseif ($question['questionType'] === 'identification') {
                ?>
                <div class="row mb-5 d-flex align-items-center justify-content-center">
                  <div class="col-md-8">
                    <h2 style="margin-bottom: 10px; color: green;">Identification</h2>
                    <div class="card" style="padding: 30px;">
                      <div class="col d-flex align-items-center justify-content-between">
                        <div class="col-md-10">
                        <h5>
                          <?php echo $question['question']; ?>
                        </h5>
                        </div>
                        <span style="color: green;">
                          <?php echo $question['questionPoint']; ?> point
                        </span>
                      </div>
                      <div class="col-md-5">
                        <input type="text" class="form-control" id="identificationAnswer"
                          name="identificationAnswer_<?php echo $question['examQuestion_id']; ?>"
                          style="border-radius: 0; border: none; border-bottom: 1px solid #ccc; outline: none;">
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              } elseif ($question['questionType'] === 'truefalse') {
                ?>
                <div class="row mb-5 d-flex align-items-center justify-content-center">
                  <div class="col-md-8">
                    <h2 style="margin-bottom: 10px; color: green;">True or False</h2>
                    <div class="card" style="padding: 30px;">
                      <div class="col mb-3 d-flex align-items-center justify-content-between">
                        <div class="col-md-10">
                        <h5>
                          <?php echo $question['question']; ?>
                        </h5>
                        </div>
                        <span style="color: green;">
                          <?php echo $question['questionPoint']; ?> point
                        </span>
                      </div>
                      <div class="col">
                        <div class="form-check mb-3 d-flex align-items-center">
                          <input class="form-check-input" type="radio"
                            name="trueFalseGroup_<?php echo $question['examQuestion_id']; ?>"
                            id="trueRadio<?php echo $question['examQuestion_id']; ?>" value="true" style="margin-right: 10px;">
                          <label class="form-check-label" for="trueRadio<?php echo $question['examQuestion_id']; ?>"
                            style="margin-top: 5px;">True</label>
                        </div>
                        <div class="form-check mb-3 d-flex align-items-center">
                          <input class="form-check-input" type="radio"
                            name="trueFalseGroup_<?php echo $question['examQuestion_id']; ?>"
                            id="falseRadio<?php echo $question['examQuestion_id']; ?>" value="false"
                            style="margin-right: 10px;">
                          <label class="form-check-label" for="falseRadio<?php echo $question['examQuestion_id']; ?>"
                            style="margin-top: 5px;">False</label>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <?php
              }
            }
          }
          displayQuestions($multipleChoiceQuestions);
          displayQuestions($identificationQuestions);
          displayQuestions($trueFalseQuestions);
          ?>
          <div class="row mb-5 d-flex align-items-center justify-content-center" style="margin-top: -20px;">
            <div class="col-md-8">
              <p class="text-body-secondary">Please make sure that you have an answer in every question before you
                submit.
              </p>
            </div>
            <div class="col-md-8">
              <button type="submit" name="submitExam" class="btn btn-success">Submit</button>
            </div>
          </div>
        </div>
      </form>
      <?php
    } else {
      $examScore = $examResult['score'];
      $examPoint = $examResult['examPoint'];
      ?>
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="card" style="border-top: 10px solid green; border-bottom: 10px solid green;">
            <div class="row mt-3">
              <div class="col-md-12">
                <p class="text-body-secondary" style="font-size: 20px;">You already answered this exam, below is your exam
                  data.</p>
              </div>
            </div>
            <div class="row align-items-center justify-content-center">
              <div class="col-md-6 order-1 order-md-0">
                <div class="align-items-center justify-content-center">
                  <h1>Student Name:</h1>
                  <p class="text-body-secondary" style="font-size: 30px;">
                    <?php echo $studentFirstName . ' ' . $studentLastName ?>
                  </p>
                </div>
              </div>
              <div class="col-md-6 order-0 order-md-1">
                <div class="align-items-center justify-content-center">
                  <h1>Exam Title:</h1>
                  <p class="text-body-secondary" style="font-size: 30px;">
                    <?php echo $examTitle ?>
                  </p>
                </div>
              </div>
            </div>
            <div class="row align-items-center justify-content-center mt-5 mb-4">
              <div class="col-md-6 order-3 order-md-2">
                <div class="align-items-center justify-content-center">
                  <h1>Exam Type:</h1>
                  <p class="text-body-secondary" style="font-size: 30px;">
                    Quarterly Assessment
                  </p>
                </div>
              </div>
              <div class="col-md-6 order-2 order-md-3">
                <div class="align-items-center justify-content-center">
                  <h1>Exam Score:</h1>
                  <p class="text-body-secondary" style="font-size: 30px;">
                    <?php echo $examScore ?> /
                    <?php echo $examPoint ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
    }
    ?>
  </div>

  <script>
    function goBack() {
      window.history.back();
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