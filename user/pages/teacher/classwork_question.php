<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'];
}

include("db_conn.php");
$teacher_id = $_SESSION['user_id'];

if (isset($_POST['ask_button'])) {
  $title = $_POST['title'];
  $question = $_POST['question'];
  $instruction = $_POST['instruction'];
  $class_name = $_POST['class_name'];
  $student = $_POST['student'];
  $type = $_POST['type'];
  $point = $_POST['point'];
  $date = date('Y-m-d');
  $due_date = $_POST['due_date'];
  $time = $_POST['time'];
  $class_topic = $_POST['class_topic'];
  $youtube = isset($_SESSION['temp_youtube']) ? $_SESSION['temp_youtube'] : '';
  $question_status = "assigned";

  if (isset($_SESSION['temp_file_id'])) {
    $file_id = $_SESSION['temp_file_id'];
    $link = $_SESSION['temp_link'];
    $file_name = $_SESSION['temp_file_name'];

    $sql = "INSERT INTO classwork_question (title, question, instruction, class_name, student, type, point, date, due_date, time, 
    class_topic, class_id, teacher_id, link, file, youtube, question_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtinsert = $conn->prepare($sql);
    $result = $stmtinsert->execute([$title, $question, $instruction, $class_name, $student, $type, $point, $date, $due_date, $time, 
    $class_topic, $class_id, $teacher_id, $link, $file_name, $youtube, $question_status]);

    if ($result) {
      // Update the used column for the associated link and file
      $sql = "UPDATE classwork_question_upload SET used = 1 WHERE link = ? OR question_upload_id = ? OR youtube = ?";
      $stmtupdate = $conn->prepare($sql);
      $stmtupdate->execute([$link, $file_id, $youtube]);

      // Remove the temporary link, file ID, and filename from the session
      unset($_SESSION['temp_link']);
      unset($_SESSION['temp_file_id']);
      unset($_SESSION['temp_file_name']);
      unset($_SESSION['temp_youtube']);

      header("Location: class_classwork.php?class_id=$class_id");
    } else {
      echo "Failed: " . $conn->error;
    }
  } else {
    $sql = "INSERT INTO classwork_question (title, question, instruction, class_name, student, type, point, date, due_date, 
    time, class_topic, class_id, teacher_id, question_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtinsert = $conn->prepare($sql);
    $result = $stmtinsert->execute([$title, $question, $instruction, $class_name, $student, $type, $point, $date, $due_date, 
    $time, $class_topic, $class_id, $teacher_id, $question_status]);

    if ($result) {
      header("Location: class_classwork.php?class_id=$class_id");
    } else {
      echo "Failed: " . $conn->error;
    }
  }
}

if (isset($_POST['add_link'])) {
  $link = $_POST['link'];
  $_SESSION['temp_link'] = $link;

  $sql = "INSERT INTO classwork_question_upload (link, used) VALUES (?, 0)";
  $stmtinsert = $conn->prepare($sql);
  $result = $stmtinsert->execute([$link]);

  if ($result) {
    $lastInsertId = $conn->insert_id;
    $_SESSION['temp_file_id'] = $lastInsertId;
  }
}

if (isset($_POST['file_submit'])) {
  if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
      $tmp_name = $file['tmp_name'];
      $file_name = basename($file['name']);

      $upload_directory = 'assets/uploads/' . $file_name;

      if (move_uploaded_file($tmp_name, $upload_directory)) {
        $sql = "INSERT INTO classwork_question_upload (file, used) VALUES (?, 0)";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$file_name]);

        if ($result) {
          $lastInsertId = $conn->insert_id;
          $_SESSION['temp_file_id'] = $lastInsertId;
          $_SESSION['temp_file_name'] = $file_name;
        } else {
          echo "Error saving file to the database.";
        }
      } else {
        echo "Error uploading the file.";
      }
    } else {
      echo "Error: File upload failed with error code " . $file['error'];
    }
  }
}

if (isset($_POST['youtube_submit'])) {
  $youtube = $_POST['youtube'];
  $_SESSION['temp_youtube'] = $youtube;

  $sql = "INSERT INTO classwork_question_upload (youtube, used) VALUES (?, 0)"; // Fix the table name here
  $stmt = $conn->prepare($sql);
  $result = $stmt->execute([$youtube]);

  if ($result) {
    $lastInsertId = $conn->insert_id;
    $_SESSION['temp_file_id'] = $lastInsertId;
  } else {
    echo "Error: " . $stmt->error; // Check for errors in query execution
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
          <p class="text-body-secondary" style="margin-top: 10px; font-size: 22px;">Question</p>
        </div>
        <div>
          <div class="btn-group">
            <button type="submit" id="ask_button" name="ask_button" class="btn btn-success"
              style="margin-right: 3px; width: 15vh; margin-bottom: 20px;">Ask</button>
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
                    placeholder="Question Title"></textarea>
                  <label for="floatingInput">Question Title</label>
                </div>
              </div>
            </div>
            <div class="row" style="padding-bottom: 8vh;">
              <div class="col-md-1" style="margin-left: 3px; margin-right: 5px;">
                <i class="bi bi-patch-question" style="font-size: 7vh;"></i>
              </div>
              <div class="col-md-8 mb-4" style="margin-left: 3px;">
                <div class="form-floating">
                  <textarea name="question" class="form-control auto-resize" id="floatingInput"
                    placeholder="Question"></textarea>
                  <label for="floatingInput">Question</label>
                </div>
              </div>
            </div>
            <div class="row" style="margin-top: -25px;">
              <div class="col-md-1" style="margin-left: 3px; margin-right: 5px;">
                <i class="bi bi-text-paragraph" style="font-size: 6vh;"></i>
              </div>
              <div class="col-md-10">
                <div class="form-floating">
                  <textarea name="instruction" class="form-control auto-resize" id="floatingInput"
                    placeholder="Instructions" style="height: 200px;"></textarea>
                  <label for="floatingInput">Instructions</label>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-1" style="margin-left: 7px;"></div>
              <div class="col-md-4 mt-4 mb-4">
                <div class="dropdown">
                  <a class="btn btn-outline-success" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-plus"></i>
                    Add File
                  </a>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#linkModal">
                        <i class="bi bi-link" style="margin-right: 10px; font-size: 20px"></i>
                        Link
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#fileModal">
                        <i class="bi bi-file-earmark-arrow-up" style="margin-right: 10px; font-size: 20px"></i>
                        File
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#youtubeModal">
                        <i class="bi bi-youtube" style="margin-right: 10px; font-size: 20px"></i>
                        Youtube
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <?php
            include("db_conn.php");

            $sql = "SELECT question_upload_id, youtube, file, link FROM classwork_question_upload WHERE used=0";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
              if (!empty($row['youtube'])) // Check if it's a YouTube link
              {
                $question_upload_id = $row['question_upload_id'];
                $youtube = $row['youtube'];
                $video_id = '';
                parse_str(parse_url($youtube, PHP_URL_QUERY), $video_id);

                if (!empty($video_id) && isset($video_id['v'])) {
                  $embedUrl = "https://www.youtube.com/embed/" . $video_id['v'];
                  ?>
                  <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-8 mb-3">
                      <div class="card">
                        <a href="<?php echo $embedUrl; ?>" target="_blank" style="text-decoration: none; margin-left: 30px;">
                          <div class="row mt-3" style="margin-bottom: -15px;">
                            <div class="col-md-9">
                              <p
                                style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                <?php echo $embedUrl ?>
                              </p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-9 text-body-secondary">
                              YOUTUBE LINK
                            </div>
                          </div>
                        </a>
                        <a
                          href="delete_mainYoutubeQuestion.php?deleteid=<?php echo $question_upload_id ?>&class_id=<?php echo $class_id ?>">
                          <div class="row mb-3">
                            <div class="col text-end" style="margin-right: 15px; font-size: 25px; color: red;">
                              <i class="bi bi-trash-fill"></i>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                  <?php
                }
              } elseif (!empty($row['file'])) {
                $question_upload_id = $row['question_upload_id'];
                $fileUrl = $row['file'];
                $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
                $displayText = ($fileExtension === 'pdf') ? 'PDF File' : strtoupper($fileExtension) . ' File';
                ?>
                <div class="row">
                  <div class="col-md-1"></div>
                  <div class="col-md-8 mb-3">
                    <div class="card">
                      <a href="<?php echo $fileUrl; ?>" target="_blank" style="text-decoration: none; margin-left: 30px;">
                        <div class="row mt-3" style="margin-bottom: -15px;">
                          <div class="col-md-9">
                            <p
                              style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                              <?php echo $fileUrl ?>
                            </p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-9 text-body-secondary">
                            <?php echo strtoupper($fileExtension) ?>
                          </div>
                        </div>
                      </a>
                      <a
                        href="delete_mainFileQuestion.php?deleteid=<?php echo $question_upload_id ?>&class_id=<?php echo $class_id ?>">
                        <div class="row mb-3">
                          <div class="col text-end" style="margin-right: 15px; font-size: 25px; color: red;">
                            <i class="bi bi-trash-fill"></i>
                          </div>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
                <?php
              } elseif (!empty($row['link'])) {
                $question_upload_id = $row['question_upload_id'];
                $linkUrl = $row['link'];
                ?>
                <div class="row">
                  <div class="col-md-1"></div>
                  <div class="col-md-8 mb-3">
                    <div class="card">
                      <a href="<?php echo $linkUrl; ?>" target="_blank" style="text-decoration: none; margin-left: 30px;">
                        <div class="row mt-3" style="margin-bottom: -15px;">
                          <div class="col-md-9">
                            <p
                              style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                              <?php echo $linkUrl ?>
                            </p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-9 text-body-secondary">
                            LINK
                          </div>
                        </div>
                      </a>
                      <a
                        href="delete_mainLinkQuestion.php?deleteid=<?php echo $question_upload_id ?>&class_id=<?php echo $class_id ?>">
                        <div class="row mb-3">
                          <div class="col text-end" style="margin-right: 15px; font-size: 25px; color: red;">
                            <i class="bi bi-trash-fill"></i>
                          </div>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
                <?php
              }
            }
            ?>
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
              <label class="text-body-secondary mb-3" style="font-size: 20px;">Written / Performance</label>
              <div class="col-md-10 mb-4">
                <input type="text" class="form-control" name="type" style="padding: 10px;">
              </div>
            </div>
            <div class="row">
              <label class="text-body-secondary mb-3" style="font-size: 20px;">Points</label>
              <div class="col-md-6 mb-4">
                <input type="text" name="point" class="form-control" style="padding: 10px;">
              </div>
            </div>
            <div class="row">
              <label class="text-body-secondary mb-3" style="font-size: 20px;">Due-date</label>
              <div class="col-md-14 mb-4">
              <input type="date" name="due_date" id="due_date" class="form-control" min="<?php echo date('Y-m-d'); ?>">
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

                  while ($row = mysqli_fetch_assoc($result)) {
                    $class_topic = $row['class_topic'];
                    echo '<option value="' . $class_topic . '">' . $class_topic . '</option>';
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
  <form action="" method="post" class="form-link" id="myLinkForm">
    <div class="modal fade" id="linkModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body mb-4">
            <h5 class="mb-4">Add Link</h5>
            <div class="form-floating">
              <input type="text" class="form-control" id="linkInput" placeholder="Link" name="link">
              <label for="linkInput">Link</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="addLink" name="add_link">Add Link</button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <form action="" method="post" class="form-link" enctype="multipart/form-data" id="myFileForm">
    <div class="modal fade" id="fileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body mb-4">
            <h5 class="mb-4">Add File</h5>
            <div class="form-floating">
              <input type="file" name="file">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" name="file_submit">Add File</button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <form action="" method="post" class="form-youtube" id="myYoutubeForm">
    <div class="modal fade" id="youtubeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body mb-4">
            <h5 class="mb-4">Add Youtube Link</h5>
            <div class="form-floating">
              <input type="text" class="form-control" id="youtubeLinkInput" name="youtube" placeholder="Link">
              <label for="youtubeLinkInput">Link</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="addYoutubeLink" name="youtube_submit">Add Youtube
              Link</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var askButton = document.getElementById("ask_button"); // Get the "Ask" button
      var form = document.querySelector('form');

      askButton.addEventListener('click', function (event) {
        var questionInput = document.querySelector('[name="question"]');
        var instructionInput = document.querySelector('[name="instruction"]');
        var typeInput = form.querySelector('[name="type"]');
        var selectedType = typeInput.value;
        var pointInput = document.querySelector('[name="point"]');
        var duedateInput = document.querySelector('[name="due_date"]');

        var isEmpty = false;

        if (questionInput.value.trim() === '') {
          isEmpty = true;
          questionInput.classList.add('is-invalid');
        } else {
          questionInput.classList.remove('is-invalid');
        }

        if (instructionInput.value.trim() === '') {
          isEmpty = true;
          instructionInput.classList.add('is-invalid');
        } else {
          instructionInput.classList.remove('is-invalid');
        }

        if ((selectedType !== "written" && selectedType !== "performance") || selectedType === "") {
        event.preventDefault();
        typeInput.classList.add('is-invalid');
        } else {
          typeInput.classList.remove('is-invalid');
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
  <script>
    const linkInput = document.getElementById("linkInput");
    const linkPattern = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/;

    document.getElementById("myLinkForm").addEventListener("submit", function (event) {
      const inputValue = linkInput.value.trim();

      if (!inputValue) {
        event.preventDefault(); // Prevent form submission if the input is empty
        linkInput.classList.add("is-invalid"); // Add invalid class to input
      } else if (!linkPattern.test(inputValue)) {
        event.preventDefault(); // Prevent form submission if the input is not a valid link
        linkInput.classList.add("is-invalid"); // Add invalid class to input
      } else {
        linkInput.classList.remove("is-invalid"); // Remove invalid class if valid
      }
    });

    linkInput.addEventListener("input", function () {
      linkInput.classList.remove("is-invalid"); // Remove invalid class on input change
    });
  </script>
  <script>
    const youtubeLinkInput = document.getElementById("youtubeLinkInput");
    const youtubeLinkPattern = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/;

    document.getElementById("myYoutubeForm").addEventListener("submit", function (event) {
      const inputValue = youtubeLinkInput.value.trim();

      if (!inputValue || !youtubeLinkPattern.test(inputValue)) {
        event.preventDefault(); // Prevent form submission
        youtubeLinkInput.classList.add("is-invalid"); // Add invalid class to input
      } else {
        youtubeLinkInput.classList.remove("is-invalid"); // Remove invalid class if valid
      }
    });

    youtubeLinkInput.addEventListener("input", function () {
      youtubeLinkInput.classList.remove("is-invalid"); // Remove invalid class on input change
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