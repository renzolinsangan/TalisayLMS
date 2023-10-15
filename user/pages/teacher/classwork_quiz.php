<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/classwork_quiz.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>

  <form action="" method="post">
    <nav class="navbar navbar-light mb-5">
      <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
          <button type="button" class="go-back" onclick="goToClasswork('<?php echo $class_id; ?>')"><i
              class="bi bi-x-lg custom-icon"></i></button>
          <p class="text-body-secondary" style="margin-top: 10px; font-size: 22px;">Quiz</p>
        </div>
        <div>
          <div class="btn-group">
            <button type="submit" name="create" class="btn btn-success"
              style="margin-right: 3px; width: 12vh; margin-bottom: 10px;">Create</button>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
              data-bs-toggle="dropdown" aria-expanded="false"
              style="margin-right: 15px; width: 5vh; height: 6vh; margin-bottom: 10px;">
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
          <div class="col-md-10 mx-auto grid-margin stretch-card">
            <div class="card" style="border-top: 15px solid green; height: 35vh;">
              <div class="card-body mt-3 mb-4" id="quiz-header">
                <input class="form-control title" type="text" name="title" value="Quiz Title" placeholder="Quiz Title">
                <input class="form-control text-body-secondary description mt-3" type="text" name="description"
                  value="Form Description" placeholder="Form Description">
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-5">
          <div class="col-md-10 mx-auto grid-margin stretch-card mb-5">
            <div class="card" id="adjustable-card-1">
              <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; height: 10vh;">
                <input class="form-control" type="text" name="question" id="adjustable-input" value="Untitled Question">
                <div class="dropdown-center" style="margin-left: 30px;">
                  <button class="btn btn-transparent btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <span id="selected-option">Type of Question</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" id="short-answer" name="short-answer"><i
                          class="bi bi-filter-left" style="margin-right: 5px;"></i>Short Answer</a></li>
                    <li><a class="dropdown-item" href="#" id="paragraph" name="paragraph"><i
                          class="bi bi-text-paragraph" style="margin-right: 5px;"></i>Paragraph</a></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#" id="multiple" name="multiple"><i class="bi bi-circle"
                          style="margin-right: 5px;"></i>Multiple Choice</a></li>
                    <li><a class="dropdown-item" href="#" id="checkbox" name="checkbox"><i
                          class="bi bi-check-square-fill" style="margin-right: 5px;"></i>Checkboxes</a></li>
                    <li><a class="dropdown-item" href="#" id="truefalse" name="truefalse"><i
                          class="bi bi-check-circle-fill" style="margin-right: 5px;"></i>True / False</a></li>
                  </ul>
                </div>
              </div>

              <div class="card-body mt-3 mb-4" id="short-answer-body" style="display: none;">
                <input class="form-control short-answer" name="short_answer" id="short_answer" type="text"
                  placeholder="Short Answer Text" style="font-size: 18px; width: 45vh;">
              </div>
              <div class="card-footer" id="short-answer-footer" style="display: none; height: 10vh;">
                <div class="d-flex align-items-center">
                  <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Answer Key and Points">
                    <a href="#" class="d-flex align-items-center" style="text-decoration: none;">
                      <div class="col-auto" style="margin-left: 10px; margin-right: 8px; margin-top: -5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor"
                          class="bi bi-clipboard2-check-fill" viewBox="0 0 16 16">
                          <path
                            d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5Z" />
                          <path
                            d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585c.055.156.085.325.085.5V2a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 4 2v-.5c0-.175.03-.344.085-.5Zm6.769 6.854-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708.708Z" />
                        </svg>
                      </div>
                      <div class="col" style="margin-top: 5px; margin-right: 15px;">
                        <h5>Answer Key</h5>
                      </div>
                    </a>
                  </div>
                  <div class="col-auto" style="margin-top: 15px;">
                    <p class="text-body-secondary">(0) points</p>
                  </div>
                  <div class="col"></div>
                  <a href="#" style="color: grey; font-size: 20px;">
                    <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </div>
                  </a>
                  <div class="dropstart" style="margin-left: 10px; margin-bottom: 2px;">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Options">
                        <i class="bi bi-three-dots-vertical" style="font-size: 20px; color: grey;"></i>
                      </div>
                    </a>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item disabled" style="font-size: 12px;">Select</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">Description</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="card-body mt-3 mb-4" id="paragraph-body" style="display: none;">
                <textarea class="form-control auto-resize" name="paragraph" id="paragraph"
                  placeholder="Long Answer Text" style="font-size: 18px; height: 5vh;"></textarea>
              </div>
              <div class="card-footer" id="paragraph-footer" style="display: none; height: 10vh;">
                <div class="d-flex align-items-center">
                  <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Answer Key and Points">
                    <a href="#" class="d-flex align-items-center" style="text-decoration: none;">
                      <div class="col-auto" style="margin-left: 10px; margin-right: 8px; margin-top: -5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor"
                          class="bi bi-clipboard2-check-fill" viewBox="0 0 16 16">
                          <path
                            d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5Z" />
                          <path
                            d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585c.055.156.085.325.085.5V2a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 4 2v-.5c0-.175.03-.344.085-.5Zm6.769 6.854-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708.708Z" />
                        </svg>
                      </div>
                      <div class="col" style="margin-top: 5px; margin-right: 15px;">
                        <h5>Answer Key</h5>
                      </div>
                    </a>
                  </div>
                  <div class="col-auto" style="margin-top: 15px;">
                    <p class="text-body-secondary">(0) points</p>
                  </div>
                  <div class="col"></div>
                  <a href="#" style="color: grey; font-size: 20px;">
                    <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </div>
                  </a>
                  <div class="dropstart" style="margin-left: 10px; margin-bottom: 2px;">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Options">
                        <i class="bi bi-three-dots-vertical" style="font-size: 20px; color: grey;"></i>
                      </div>
                    </a>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item disabled" style="font-size: 12px;">Select</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">Description</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="card-body mt-3 mb-4" id="multiple-body" style="display: none;">
                <div class="row align-items-center">
                  <div class="col-auto" style="font-size: 20px; margin-right: -10px;">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                  </div>
                  <div class="col" style="margin-right: -5px;">
                    <input class="form-control radio" type="text" value="Option 1" style="font-size: 18px;">
                  </div>
                  <div class="col-auto mt-2">
                    <a class="#" href="#" style="color: #ccc; font-size: 25px; margin-right: 5px;">
                      <i class="bi bi-x-lg"></i>
                    </a>
                  </div>
                </div>
                <div class="radio-container mt-3">

                </div>
                <div class="row align-items-center mt-4">
                  <div class="col-auto" style="font-size: 20px; margin-right: -10px;">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="addRadioOption"
                      style="margin-right: 10px;">
                    <span class="text-body-secondary">Add another option</span>
                  </div>
                </div>
              </div>
              <div class="card-footer" id="multiple-footer" style="display: none; height: 10vh;">
                <div class="d-flex align-items-center">
                  <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Answer Key and Points">
                    <a href="#" class="d-flex align-items-center" style="text-decoration: none;">
                      <div class="col-auto" style="margin-left: 10px; margin-right: 8px; margin-top: -5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor"
                          class="bi bi-clipboard2-check-fill" viewBox="0 0 16 16">
                          <path
                            d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5Z" />
                          <path
                            d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585c.055.156.085.325.085.5V2a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 4 2v-.5c0-.175.03-.344.085-.5Zm6.769 6.854-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708.708Z" />
                        </svg>
                      </div>
                      <div class="col" style="margin-top: 5px; margin-right: 15px;">
                        <h5>Answer Key</h5>
                      </div>
                    </a>
                  </div>
                  <div class="col-auto" style="margin-top: 15px;">
                    <p class="text-body-secondary">(0) points</p>
                  </div>
                  <div class="col"></div>
                  <a href="#" style="color: grey; font-size: 20px;">
                    <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </div>
                  </a>
                  <div class="dropstart" style="margin-left: 10px; margin-bottom: 2px;">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Options">
                        <i class="bi bi-three-dots-vertical" style="font-size: 20px; color: grey;"></i>
                      </div>
                    </a>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item disabled" style="font-size: 12px;">Select</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">Description</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="card-body mt-3 mb-4" id="checkbox-body" style="display: none;">
                <div class="row align-items-center">
                  <div class="col-auto" style="font-size: 20px; margin-right: -10px;">
                    <input class="form-check-input" type="checkbox" name="flexCheckDefault" id="flexCheckDefault1">
                  </div>
                  <div class="col" style="margin-right: -5px;">
                    <input class="form-control checkbox" type="text" value="Option 1" style="font-size: 18px;">
                  </div>
                  <div class="col-auto mt-2">
                    <a class="#" href="#" style="color: #ccc; font-size: 25px; margin-right: 5px;">
                      <i class="bi bi-x-lg"></i>
                    </a>
                  </div>
                </div>
                <div class="checkbox-container mt-3">

                </div>
                <div class="row align-items center mt-4">
                  <div class="col-auto" style="font-size: 20px; margin-right: -10px;">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="addCheckBoxOption"
                      style="margin-right: 10px;">
                    <span class="text-body-secondary">Add another option</span>
                  </div>
                </div>
              </div>
              <div class="card-footer" id="checkbox-footer" style="display: none; height: 10vh;">
                <div class="d-flex align-items-center">
                  <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Answer Key and Points">
                    <a href="#" class="d-flex align-items-center" style="text-decoration: none;">
                      <div class="col-auto" style="margin-left: 10px; margin-right: 8px; margin-top: -5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor"
                          class="bi bi-clipboard2-check-fill" viewBox="0 0 16 16">
                          <path
                            d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5Z" />
                          <path
                            d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585c.055.156.085.325.085.5V2a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 4 2v-.5c0-.175.03-.344.085-.5Zm6.769 6.854-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708.708Z" />
                        </svg>
                      </div>
                      <div class="col" style="margin-top: 5px; margin-right: 15px;">
                        <h5>Answer Key</h5>
                      </div>
                    </a>
                  </div>
                  <div class="col-auto" style="margin-top: 15px;">
                    <p class="text-body-secondary">(0) points</p>
                  </div>
                  <div class="col"></div>
                  <a href="#" style="color: grey; font-size: 20px;">
                    <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </div>
                  </a>
                  <div class="dropstart" style="margin-left: 10px; margin-bottom: 2px;">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Options">
                        <i class="bi bi-three-dots-vertical" style="font-size: 20px; color: grey;"></i>
                      </div>
                    </a>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item disabled" style="font-size: 12px;">Select</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">Description</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="card-body mt-3 mb-4" id="truefalse-body" style="display: none;">
                <div class="row">
                  <div class="col">
                    <div class="form-check mb-4" style="margin-left: 20px;">
                      <input class="form-check-input" type="radio" name="myRadioGroup" id="true-radio"
                        style="font-size: 20px;">
                      <label class="form-check-label" for="true-radio" style="font-size: 20px;">
                        True
                      </label>
                    </div>
                    <div class="form-check" style="margin-left: 20px;">
                      <input class="form-check-input" type="radio" name="myRadioGroup" id="false-radio"
                        style="font-size: 20px;">
                      <label class="form-check-label" for="false-radio" style="font-size: 20px;">
                        False
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer" id="truefalse-footer" style="display: none; height: 10vh;">
                <div class="d-flex align-items-center">
                  <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Answer Key and Points">
                    <a href="#" class="d-flex align-items-center" style="text-decoration: none;">
                      <div class="col-auto" style="margin-left: 10px; margin-right: 8px; margin-top: -5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor"
                          class="bi bi-clipboard2-check-fill" viewBox="0 0 16 16">
                          <path
                            d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5Z" />
                          <path
                            d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585c.055.156.085.325.085.5V2a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 4 2v-.5c0-.175.03-.344.085-.5Zm6.769 6.854-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708.708Z" />
                        </svg>
                      </div>
                      <div class="col" style="margin-top: 5px; margin-right: 15px;">
                        <h5>Answer Key</h5>
                      </div>
                    </a>
                  </div>
                  <div class="col-auto" style="margin-top: 15px;">
                    <p class="text-body-secondary">(0) points</p>
                  </div>
                  <div class="col"></div>
                  <a href="#" style="color: grey; font-size: 20px;">
                    <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </div>
                  </a>
                  <div class="dropstart" style="margin-left: 10px; margin-bottom: 2px;">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Options">
                        <i class="bi bi-three-dots-vertical" style="font-size: 20px; color: grey;"></i>
                      </div>
                    </a>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item disabled" style="font-size: 12px;">Select</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">Description</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-container mt-3">
            </div>
            <div class="col-md-4 mt-4 mb-2 mx-auto grid-margin stretch-card">
              <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Question">
                    <button type="button" id="addQuestionButton" onclick="addQuestionCard()"
                      style="border: none; background-color: transparent;">
                      <i class="bi bi-plus-circle" style="font-size: 22px; color: grey;"></i>
                    </button>
                  </div>
                  <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Title and Description">
                    <button style="border: none; background-color: transparent;">
                      <i class="bi bi-chat-square-dots" style="font-size: 22px; color: grey;"></i>
                    </button>
                  </div>
                  <a href="#">
                    <div data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Section">
                      <i class="bi bi-collection" style="font-size: 22px; color: grey;"></i>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script>
    function goToClasswork(classId) {
      window.location.href = `class_classwork.php?class_id=${classId}`;
    }

    $(function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    });

    $(function () {
      $('.dropstart [data-bs-toggle="dropdown"]').dropdown();
    });
  </script>
  <script>
    const optionData = {
      "short-answer": {
        text: "Short Answer",
        icon: '<i class="bi bi-filter-left" style="margin-right: 5px;"></i>'
      },
      "paragraph": {
        text: "Paragraph",
        icon: '<i class="bi bi-text-paragraph" style="margin-right: 5px;"></i>'
      },
      "multiple": {
        text: "Multiple Choice",
        icon: '<i class="bi bi-circle" style="margin-right: 5px;"></i>'
      },
      "checkbox": {
        text: "Checkboxes",
        icon: '<i class="bi bi-check-square-fill" style="margin-right: 5px;"></i>'
      },
      "truefalse": {
        text: "True/False",
        icon: '<i class="bi bi-check-circle-fill" style="margin-right: 5px;"></i>'
      }
    };

    function toggleCard(option) {
      document.querySelectorAll(".card-body:not(#quiz-header), .card-footer").forEach(function (element) {
        element.style.display = "none";
      });

      if (option in optionData) {
        document.getElementById(option + "-body").style.display = "block";
        document.getElementById(option + "-footer").style.display = "block";

        const selectedOptionText = document.getElementById("selected-option");
        selectedOptionText.innerHTML = optionData[option].icon + optionData[option].text;
      }
    }

    document.querySelectorAll(".dropdown-item").forEach(function (item) {
      item.addEventListener("click", function (e) {
        e.preventDefault();
        const option = e.currentTarget.id;
        toggleCard(option);
      });
    });
  </script>
  <script>
    function addRadioOption() {
      const radioContainer = document.querySelector('.radio-container');
      const optionCount = radioContainer.querySelectorAll('.form-check-input').length;

      const newRow = document.createElement('div');
      newRow.classList.add('row', 'align-items-center', 'mb-3');

      const newOption = `
      <div class="col-auto" style="font-size: 20px; margin-right: -10px;">
        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault${optionCount + 1}">
      </div>
      <div class="col" style="margin-right: -5px;">
        <input class="form-control radio" type="text" value="Option ${optionCount + 2}" style="font-size: 18px;">
      </div>
      <div class="col-auto mt-2">
        <a class="delete_option" href="#" style="color: #ccc; font-size: 25px; margin-right: 5px;">
          <i class="bi bi-x-lg"></i>
        </a>
      </div>
      `;

      newRow.innerHTML = newOption;
      radioContainer.appendChild(newRow);

      clearOtherRadios(document.getElementById(`flexRadioDefault${optionCount + 1}`));
    }

    function clearOtherRadios(clickedRadio) {
      const radios = document.querySelectorAll('[name="flexRadioDefault"]');
      radios.forEach(radio => {
        if (radio !== clickedRadio) {
          radio.checked = false;
        }
      });
    }

    document.getElementById('addRadioOption').addEventListener('click', function () {
      addRadioOption();
    });
  </script>
  <script>
    function addCheckBoxOption() {
      const checkboxContainer = document.querySelector('.checkbox-container');
      const optionCount = checkboxContainer.querySelectorAll('.form-check-input[type="checkbox"]').length;

      const newRow = document.createElement('div');
      newRow.classList.add('row', 'align-items-center', 'mb-3');

      const newOption = `
      <div class="col-auto" style="font-size: 20px; margin-right: -10px;">
        <input class="form-check-input" type="checkbox" name="flexCheckDefault" id="flexCheckDefault${optionCount + 1}">
      </div>
      <div class="col" style="margin-right: -5px;">
        <input class="form-control checkbox" type="text" value="Option ${optionCount + 2}" style="font-size: 18px;">
      </div>
      <div class="col-auto mt-2">
        <a class="#" href="#" style="color: #ccc; font-size: 25px; margin-right: 5px;">
          <i class="bi bi-x-lg"></i>
        </a>
      </div>
      `;

      newRow.innerHTML = newOption;
      checkboxContainer.appendChild(newRow);

      clearOtherCheckboxes(document.getElementById(`flexCheckDefault${optionCount + 1}`));
      clearRadio();
    }

    function clearOtherCheckboxes(clickedCheckbox) {
      const checkboxes = document.querySelectorAll('[name="flexCheckDefault"]');
      checkboxes.forEach(checkbox => {
        if (checkbox !== clickedCheckbox) {
          checkbox.checked = false;
        }
      });
    }

    function clearRadio() {
      const radio = document.getElementById('addCheckBoxOption');
      radio.checked = false;
    }

    document.getElementById('addCheckBoxOption').addEventListener('click', function () {
      addCheckBoxOption();
    });
  </script>
  <script>
    const textarea = document.querySelector(".auto-resize");
    const initialHeight = textarea.scrollHeight + "px";

    textarea.addEventListener("input", function () {
      this.style.height = initialHeight;
      this.style.height = (this.scrollHeight <= this.clientHeight) ? initialHeight : this.scrollHeight + "px";
    }); 
  </script>
  <script>
    let cardCount = 1;

    document.getElementById("addQuestionButton").addEventListener("click", function () {
      cardCount++;
      const originalCard = document.getElementById("adjustable-card-1");
      const newCard = originalCard.cloneNode(true);

      // Update the new card's ID to make it unique
      newCard.id = `adjustable-card-${cardCount}`;

      // Update any other elements in the cloned card if necessary
      const inputElement = newCard.querySelector(".form-control");
      inputElement.value = "Untitled Question";

      // Clear the selected question type for the new card
      const selectedOption = newCard.querySelector("#selected-option");
      selectedOption.textContent = "Type of Question";

      // Hide all card-body and card-footer elements
      const cardBodies = newCard.querySelectorAll(".card-body");
      const cardFooters = newCard.querySelectorAll(".card-footer");

      cardBodies.forEach((body) => {
        body.style.display = "none";
      });

      cardFooters.forEach((footer) => {
        footer.style.display = "none";
      });

      // Apply margin-bottom to the new card
      newCard.style.marginBottom = "20px";

      // Attach event listeners for the dropdown menu items to show the appropriate body and footer
      const dropdownItems = newCard.querySelectorAll(".dropdown-item");
      dropdownItems.forEach((item) => {
        item.addEventListener("click", function (event) {
          const selectedOption = newCard.querySelector("#selected-option");
          selectedOption.textContent = item.textContent;
          const questionType = item.getAttribute("name");

          // Show the specific card-body and card-footer for the selected question type
          const body = newCard.querySelector(`#${questionType}-body`);
          const footer = newCard.querySelector(`#${questionType}-footer`);

          if (body && footer) {
            body.style.display = "block";
            footer.style.display = "block";
          }
        });
      });
      

      // Find the card container and append the new card to it
      const cardContainer = document.querySelector(".card-container");
      cardContainer.appendChild(newCard);
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
    crossorigin="anonymous"></script>
</body>

</html>