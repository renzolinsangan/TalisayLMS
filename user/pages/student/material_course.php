<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../user_login.php");
  exit();
}

$class_id = $_GET['class_id'];
$material_id = $_GET['material_id'];

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

    $sql_get_material_info = "SELECT title, description, date, link, file, youtube FROM classwork_material WHERE teacher_id=? AND material_id=?";
    $stmt_get_material_info = $db->prepare($sql_get_material_info);
    $stmt_get_material_info->execute([$teacher_id, $material_id]);
    $material_data = $stmt_get_material_info->fetch(PDO::FETCH_ASSOC);

    if ($material_data) {
      $title = $material_data['title'];
      $description = $material_data['description'];
      $date = $material_data['date'];
      $formatted_date = date("F j", strtotime($date));
      $link = $material_data['link'];
      $file = $material_data['file'];
      $youtube = $material_data['youtube'];

      $fileDirectory = "../teacher/assets/uploads/";
      $filePath = $fileDirectory . $file;
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
  <link rel="stylesheet" type="text/css" href="assets/css/material_course.css">
  <link rel="shortcut icon" href="../../images/trace.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>

  <nav class="navbar navbar-light fs-3 mb-5">
    <div class="d-flex align-items-center justify-content-between w-100">
      <div class="d-flex align-items-center" style="margin-top: -3px;">
        <button type="button" class="go-back" onclick="goToClasswork('<?php echo $class_id; ?>')"><i
            class="bi bi-arrow-bar-left" style="color: white;"></i></button>
            <p class="name" style="margin-top: 6px; font-size: 22px; pointer-events: none; color: white;">
              Material
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
              style="display: inline-block; background-color: green; border-radius: 50%; width: 48px; height: 48px; text-align: center; margin-right: 10px; margin-bottom: 30px;">
              <i class="bi bi-journal-text" style="color: white; line-height: 48px; font-size: 30px;"></i>
            </div>
            <div>
              <h2>
                <?php echo $title ?>
              </h2>
              <p class="text-body-secondary">
                <?php echo $first_name . " " . $last_name ?>
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-11 col-sm-12">
          <p class="text-end text-body-secondary" style="margin-top: -40px;">Posted
            <?php echo $formatted_date ?>
          </p>
        </div>
        <hr class="divider" style="border-top: 2px solid black; width: 90%; margin-top: 10px; margin-left: 15px;">
      </div>
      <div class="row justify-content-left align-items-center">
        <div class="col-md-9">
          <?php echo $description ?>
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
  <script type="text/javascript" src="js/virtual-select.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
    crossorigin="anonymous"></script>
</body>

</html>