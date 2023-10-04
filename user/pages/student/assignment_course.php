<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'];
$assignment_id = $_GET['assignment_id'];

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

    $sql_get_assignment_info = "SELECT title, instruction, point, due_date, time, link, file, youtube FROM classwork_assignment WHERE teacher_id=? AND assignment_id=?";
    $stmt_get_assignment_info = $db->prepare($sql_get_assignment_info);
    $stmt_get_assignment_info->execute([$teacher_id, $assignment_id]);
    $assignment_data = $stmt_get_assignment_info->fetch(PDO::FETCH_ASSOC);

    if ($assignment_data) {
      $title = $assignment_data['title'];
      $instruction = $assignment_data['instruction'];
      $point = $assignment_data['point'];
      $due_date = $assignment_data['due_date'];
      $formatted_due_date = date("F j", strtotime($due_date));
      $time = $assignment_data['time'];
      $link = $assignment_data['link'];
      $file = $assignment_data['file'];
      $youtube = $assignment_data['youtube'];

      $fileDirectory = "../teacher/assets/uploads/";
      $filePath = $fileDirectory . $file;
    }
  }
}

if (isset($_POST['add_link'])) {
  $link = $_POST['link'];
  $_SESSION['temp_link'] = $link;

  $sql = "INSERT INTO assignment_course_upload (link, class_id, user_id, assignment_id) VALUES (?, ?, ?, ?)";
  $stmtinsert = $db->prepare($sql);
  $result = $stmtinsert->execute([$link, $class_id, $user_id, $assignment_id]);

  if ($result) {
    header("Location: assignment_course.php?class_id=$class_id&assignment_id=$assignment_id");
    exit();
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
        $sql = "INSERT INTO assignment_course_upload (file, class_id, user_id, assignment_id) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([$file_name, $class_id, $user_id, $assignment_id]);

        if ($result) {
          $_SESSION['temp_file_name'] = $file_name;

          header("Location: assignment_course.php?class_id=$class_id&assignment_id=$assignment_id");
          exit;
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
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Talisay Senior High School LMS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/virtual-select.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/assignment_course.css">
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
        <h3 class="greater" style="margin-left: 10px; margin-top: 5px; margin-right: 10px; pointer-events: none;">></h3>
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
              <i class="bi bi-journal-text" style="color: white; line-height: 48px; font-size: 30px;"></i>
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
        <div class="col-md-8 col-sm-12">
          <p class="text-end text-body-secondary" style="margin-top: -40px;">Due
            <?php echo $formatted_due_date . ", " . $time ?>
          </p>
        </div>
        <div class="divider mb-3" id="divider"></div>
      </div>
      <div class="row justify-content-left align-items-center">
        <div class="col-md-7">
          <?php echo $instruction ?>
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
      <form action="" method="post">
        <div class="row justify-content-left align-items-center mb-5" id="submit-card">
          <div class="col-md-8"></div>
          <div class="col-md-4 mt-5">
            <div class="card">
              <div class="row justify-content-between">
                <div class="col mt-4" style="margin-left: 25px;">
                  <h5>Your Work</h5>
                </div>
                <div class="col text-end mt-4" style="margin-right: 25px;">
                  <p class="text-body-secondary">Assigned</p>
                </div>
              </div>
              <?php
              $sql = "SELECT assignment_course_upload_id, link, file FROM assignment_course_upload WHERE class_id=? AND assignment_id=?";
              $stmt = $db->prepare($sql);
              $stmt->execute([$class_id, $assignment_id]);

              while ($row = $stmt->fetch()) {
                $file=$row['file'];
                if (!empty($row['link'])) {
                  $assignment_course_upload_id = $row['assignment_course_upload_id'];
                  $link = $row['link'];
                  ?>
                  <div class="row justify-content-center align-items-center mt-3 mb-3">
                    <div class="col-md-11">
                      <div class="card">
                        <a href="<?php echo $link ?>" target="_blank" style="text-decoration: none; margin-left: 15px;">
                          <div class="row mt-3" style="margin-bottom: -15px;">
                            <div class="col-md-11">
                              <p
                                style="color: green; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                <?php echo $link ?>
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
                          href="delete_link_assignment.php?deleteid=<?php echo $assignment_course_upload_id ?>&class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>">
                          <div class="row mb-2">
                            <div class="col text-end" style="margin-right: 15px; font-size: 25px; color: red;">
                              <i class="bi bi-trash-fill trash-icon"></i>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                  <?php
                } elseif (!empty($row['file'])) {
                  $assignment_course_upload_id = $row['assignment_course_upload_id'];
                  $fileUrl = $row['file'];
                  $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
                  $displayText = ($fileExtension === 'pdf') ? 'PDF File' : strtoupper($fileExtension) . ' File';
                  ?>
                  <div class="row justify-content-center align-items-center mt-3 mb-3">
                    <div class="col-md-11">
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
                          href="delete_file_assignment.php?deleteid=<?php echo $assignment_course_upload_id ?>&class_id=<?php echo $class_id ?>&assignment_id=<?php echo $assignment_id ?>">
                          <div class="row mb-2">
                            <div class="col text-end" style="margin-right: 15px; font-size: 25px; color: red;">
                              <i class="bi bi-trash-fill trash-icon"></i>
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
              <div class="row justify-content-center align-items-center mt-2 mb-4">
                <div class="d-grid gap-2 col-11 mx-auto">
                  <div class="dropdown">
                    <div class="d-grid gap-2 col-12 mx-auto">
                      <button class="btn btn-outline-success mb-2" type="button" data-bs-toggle="dropdown">+ Add or
                        Create</button>
                      <ul class="dropdown-menu w-100">
                        <li>
                          <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#linkModal"
                            style="font-size: 18px; cursor: pointer;">
                            <i class="bi bi-link" style="margin-right: 10px; font-size: 23px"></i>
                            Link
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#fileModal"
                            style="font-size: 18px; cursor: pointer;">
                            <i class="bi bi-file-earmark-arrow-up" style="margin-right: 10px; font-size: 23px"></i>
                            File
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <?php
                  if (isset($_POST['mark_done'])) {
                    $sql = "INSERT INTO student_assignment_course_answer (assignment_course_upload_id, assignment_id, assignment_link, assignment_file, user_id, class_id, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute([$assignment_course_upload_id, $assignment_id, $link, $file, $user_id, $class_id, $teacher_id]);
                  }
                  ?>
                  <button class="btn btn-success" id="turnInButton" name="mark_done" type="submit">Turn-in</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
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

  <script>
    function goToClasswork(classId) {
      window.location.href = `class_course.php?class_id=${classId}`;
    }
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