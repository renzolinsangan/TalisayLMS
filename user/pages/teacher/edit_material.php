<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user_login.php");
    exit();
}

if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
}

if (isset($_SESSION['user_id'])) {
    $teacher_id = $_SESSION['user_id'];
}

include("db_conn.php");

$_SESSION['id'] = $_GET['updateid'];
$id = $_SESSION['id'];

$sql = "SELECT title, description FROM classwork_material WHERE material_id=? AND class_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $class_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$title = $row['title'];
$description = $row['description'];

$teacher_id = $_SESSION['user_id'];

if (isset($_POST['post_button'])) {
    $class_id = $_GET['class_id'];
    $teacher_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $class_name = $_POST['class_name'];
    $student = $_POST['student'];
    $class_topic = $_POST['class_topic'];
    $date = date('Y-m-d');
    $youtube = isset($_SESSION['temp_youtube']) ? $_SESSION['temp_youtube'] : '';

    // Check if there's a temporary file ID in the session
    if (isset($_SESSION['temp_file_id'])) {
        $file_id = $_SESSION['temp_file_id'];
        $link = $_SESSION['temp_link'];
        $file_name = $_SESSION['temp_file_name']; // Retrieve the filename from the session

        $sql = "UPDATE classwork_material SET title = ?, description = ?, class_name = ?, student = ?, class_topic = ?, class_id = ?, teacher_id = ?, date = ?, link = ?, file = ?, youtube = ? WHERE teacher_id = ? AND class_id = ?";
        $stmtinsert = mysqli_prepare($conn, $sql);

        if ($stmtinsert === false) {
            echo "Error in preparing the update statement: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmtinsert, "sssssiissssii", $title, $description, $class_name, $student, $class_topic, $class_id, $teacher_id, $date, $link, $file_name, $youtube, $teacher_id, $class_id);

            if (mysqli_stmt_execute($stmtinsert)) {
                // Update the used column for the associated link and file
                $sql = "UPDATE classwork_material_upload SET used = 1 WHERE link = ? OR material_upload_id = ? OR youtube = ?";
                $stmtupdate = $conn->prepare($sql);

                if ($stmtupdate === false) {
                    echo "Error in preparing the update statement for classwork_material_upload: " . mysqli_error($conn);
                } else {
                    $stmtupdate->execute([$link, $file_id, $youtube]);

                    // Remove the temporary link, file ID, and filename from the session
                    unset($_SESSION['temp_link']);
                    unset($_SESSION['temp_file_id']);
                    unset($_SESSION['temp_file_name']);
                    unset($_SESSION['temp_youtube']);

                    header("Location: class_classwork.php?class_id=$class_id");
                }
            } else {
                echo "Failed to execute the update statement: " . $stmtinsert->error;
            }
        }
    } else {
        $sql = "UPDATE classwork_material SET title = ?, description = ?, class_name = ?, student = ?, class_topic = ?, class_id = ?, teacher_id = ?, date = ? WHERE teacher_id = ? AND class_id = ?";
        $stmtinsert = mysqli_prepare($conn, $sql);

        if ($stmtinsert === false) {
            echo "Error in preparing the update statement: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmtinsert, "sssssiisii", $title, $description, $class_name, $student, $class_topic, $class_id, $teacher_id, $date, $teacher_id, $class_id);

            if (mysqli_stmt_execute($stmtinsert)) {
                header("Location: class_classwork.php?class_id=$class_id");
            } else {
                echo "Failed to execute the update statement: " . $stmtinsert->error;
            }
        }
    }
}


if (isset($_POST['add_link'])) {
    $link = $_POST['link'];
    $_SESSION['temp_link'] = $link;

    $sql = "INSERT INTO classwork_material_upload (link, used) VALUES (?, 0)";
    $stmtinsert = $conn->prepare($sql);
    $result = $stmtinsert->execute([$link]);

    if ($result) {
        $lastInsertId = $conn->insert_id;
        $_SESSION['temp_file_id'] = $lastInsertId; // Store the ID in the session
    }
}

if (isset($_POST['file_submit'])) {
    // Check if a file was uploaded
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // Check for file errors
        if ($file['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $file['tmp_name'];
            $file_name = basename($file['name']); // Get just the filename

            $upload_directory = 'assets/uploads/' . $file_name; // Include the directory again

            // Move the uploaded file to the specified directory
            if (move_uploaded_file($tmp_name, $upload_directory)) {
                $sql = "INSERT INTO classwork_material_upload (file, used) VALUES (?, 0)";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([$file_name]); // Insert just the filename, not the full path

                if ($result) {
                    $lastInsertId = $conn->insert_id;
                    $_SESSION['temp_file_id'] = $lastInsertId;
                    $_SESSION['temp_file_name'] = $file_name; // Store the filename in the session
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

    $sql = "INSERT INTO classwork_material_upload (youtube, used) VALUES (?, 0)"; // Fix the table name here
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
    <link rel="stylesheet" type="text/css" href="assets/css/cw_material.css">
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
                    <p class="text-body-secondary" style="margin-top: 10px; font-size: 22px;">Edit Material</p>
                </div>
                <div>
                    <div class="btn-group">
                        <button type="submit" name="post_button" class="btn btn-success"
                            style="margin-right: 3px; width: 15vh; margin-bottom: 20px;">Update</button>
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
                    <div class="col-md-7 mb-3">
                        <div class="row" style="padding-bottom: 8vh;">
                            <div class="col-md-1" style="margin-left: 3px; margin-right: 5px;">
                                <i class="bi bi-file-earmark-post" style="font-size: 7vh;"></i>
                            </div>
                            <div class="col-md-8" style="margin-left: 3px;">
                                <div class="form-floating">
                                    <textarea name="title" class="form-control auto-resize" id="floatingInput"
                                        placeholder="Title"><?php echo $title ?></textarea>
                                    <label for="floatingInput">Title</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1" style="margin-left: 3px; margin-right: 5px;">
                                <i class="bi bi-text-paragraph" style="font-size: 6vh;"></i>
                            </div>
                            <div class="col-md-10">
                                <div class="form-floating">
                                    <textarea name="description" class="form-control auto-resize" id="floatingInput"
                                        placeholder="Instructions"
                                        style="height: 200px;"><?php echo $description ?></textarea>
                                    <label for="floatingInput">Description</label>
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
                                                <i class="bi bi-file-earmark-arrow-up"
                                                    style="margin-right: 10px; font-size: 20px"></i>
                                                File
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#youtubeModal">
                                                <i class="bi bi-youtube"
                                                    style="margin-right: 10px; font-size: 20px"></i>
                                                Youtube
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                        include("db_conn.php");

                        $sql = "SELECT material_upload_id, youtube, file, link FROM classwork_material_upload WHERE used=0";
                        $result = mysqli_query($conn, $sql);

                        while ($row = mysqli_fetch_assoc($result)) {
                            if (!empty($row['youtube'])) // Check if it's a YouTube link
                            {
                                $material_upload_id = $row['material_upload_id'];
                                $youtube = $row['youtube'];
                                $video_id = '';
                                parse_str(parse_url($youtube, PHP_URL_QUERY), $video_id);

                                if (!empty($video_id) && isset($video_id['v'])) {
                                    $embedUrl = "https://www.youtube.com/embed/" . $video_id['v'];
                                    $id = $_SESSION['id'];
                                    ?>
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-8 mb-3">
                                            <div class="card">
                                                <a href="<?php echo $embedUrl; ?>" target="_blank"
                                                    style="text-decoration: none; margin-left: 30px;">
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
                                                    href="delete_youtube_material.php?deleteid=<?php echo $material_upload_id ?>&updateid=<?php echo $id ?>&class_id=<?php echo $class_id ?>">
                                                    <div class="row mb-3">
                                                        <div class="col text-end"
                                                            style="margin-right: 15px; font-size: 25px; color: red;">
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
                                $material_upload_id = $row['material_upload_id'];
                                $fileUrl = $row['file'];
                                $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
                                $displayText = ($fileExtension === 'pdf') ? 'PDF File' : strtoupper($fileExtension) . ' File';
                                $id = $_SESSION['id'];
                                ?>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-8 mb-3">
                                        <div class="card">
                                            <a href="<?php echo $fileUrl; ?>" target="_blank"
                                                style="text-decoration: none; margin-left: 30px;">
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
                                                href="delete_file_material.php?deleteid=<?php echo $material_upload_id ?>&updateid=<?php echo $id ?>&class_id=<?php echo $class_id ?>">
                                                <div class="row mb-3">
                                                    <div class="col text-end"
                                                        style="margin-right: 15px; font-size: 25px; color: red;">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } elseif (!empty($row['link'])) {
                                $material_upload_id = $row['material_upload_id'];
                                $linkUrl = $row['link'];
                                $id = $_SESSION['id'];
                                ?>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-8 mb-3">
                                        <div class="card">
                                            <a href="<?php echo $linkUrl; ?>" target="_blank"
                                                style="text-decoration: none; margin-left: 30px;">
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
                                                href="delete_link_material.php?deleteid=<?php echo $material_upload_id ?>&updateid=<?php echo $id ?>&class_id=<?php echo $class_id ?>">
                                                <div class="row mb-3">
                                                    <div class="col text-end"
                                                        style="margin-right: 15px; font-size: 25px; color: red;">
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
                    <div class="col-md-1  d-none d-md-block border-end"
                        style="margin-top: -48px; margin-left: -20px; margin-right: 20px;"></div>
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
                                <select id="multipleSelect" multiple name="student" placeholder="Select Students"
                                    data-search="true" data-silent-initial-value-set="true" style="height: 45px;">
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
                            <label class="text-body-secondary mb-3" style="font-size: 20px;">For</label>
                            <div class="col-md-12 mb-4">
                                <select id="multipleSelect" name="class_topic" placeholder="Select Topics"
                                    data-search="true" data-silent-initial-value-set="true" style="height: 45px;">
                                    <?php
                                    include("db_conn.php");
                                    $teacher_id = $_SESSION['user_id'];

                                    $sql_topic = "SELECT class_topic FROM topic WHERE teacher_id = $teacher_id AND class_id = $class_id";
                                    $stmt_result = mysqli_query($conn, $sql_topic);
                                    $matchingOptionFound = false;

                                    while ($row = mysqli_fetch_assoc($stmt_result)) {
                                        $class_topic = $row['class_topic'];
                                        $id = $_SESSION['id'];
                                        $class_id = $_GET['class_id'];

                                        $sql_materialTopic = "SELECT class_topic FROM classwork_material WHERE material_id = $id AND class_id = $class_id";
                                        $stmt_result_materialTopic = mysqli_query($conn, $sql_materialTopic);

                                        while ($row_materialTopic = mysqli_fetch_assoc($stmt_result_materialTopic)) {
                                            $class_topicMaterial = $row_materialTopic['class_topic'];
                                            $selected = ($class_topicMaterial == $class_topic) ? 'selected' : '';
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
                            <input type="text" class="form-control" id="youtubeLinkInput" name="youtube"
                                placeholder="Link">
                            <label for="youtubeLinkInput">Link</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addYoutubeLink" name="youtube_submit">Add
                            Youtube
                            Link</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        var form = document.querySelector('form');

        form.addEventListener('submit', function (event) {
            var titleInput = document.querySelector('[name="title"]');
            var descriptionInput = document.querySelector('[name="description"]');
            var topicDropdown = document.querySelector('[name="class_topic"]');

            var isEmpty = false;

            if (titleInput.value.trim() === '') {
                isEmpty = true;
                titleInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                titleInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (descriptionInput.value.trim() === '') {
                isEmpty = true;
                descriptionInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                descriptionInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (isEmpty) {
                event.preventDefault();
                // Optionally, you can show a validation message/alert here
            }     });
    </script>
    <script type="text/javascript" src="js/virtual-select.min.js"></script>
    <script>
        VirtualSelect.init({
            ele: '#multipleSelect'     });
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
            });     });
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
            linkInput.classList.remove("is-invalid"); // Remove invalid class on input change     });
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
            youtubeLinkInput.classList.remove("is-invalid"); // Remove invalid class on input change     });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
</body>

</html>