<?php
session_start();
include_once("db_conn.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../../admin_login.php");
    exit();
}

$id = $_GET['updateid'];
$sql = "SELECT * FROM news WHERE news_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$title = $row['title'];
$type = $row['type'];
$name = $row['name'];
$date = $row['date'];
$track = $row['track'];
$startDate = $row['start_date'];
$endDate = $row['end_date'];
$detail = $row['detail'];
$attachment = $row['attachment'];

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $name = $_POST['name'];
    $date = date("Y-m-d");
    $track = $_POST['track'];
    $startDate = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $detail = $_POST['detail'];

    // Check if a new image file is uploaded
    if (!empty($_FILES['attachment']['name'])) {
        // New image file is uploaded
        $attachment = basename($_FILES['attachment']['name']);

        // Upload the new image file and update the database
        $uploadDirectory = 'assets/image/announcement_upload/';
        $uploadPath = $uploadDirectory . $attachment;

        // Check if the file is an image
        $fileExtension = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

        if (in_array($fileExtension, $allowedExtensions)) {
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadPath)) {
                // File uploaded successfully, update the attachment field in the database
                $sql = "UPDATE news SET title=?, type=?, name=?, date=?, track=?, start_date=?, end_date=?, detail=?, attachment=? WHERE news_id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssssssssi", $title, $type, $name, $date, $track, $startDate, $endDate, $detail, $attachment, $id);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: announcement.php?msg=Announcement Updated Successfully!");
                } else {
                    echo "Failed: " . mysqli_error($conn);
                }
            } else {
                echo "Failed to upload the file.";
            }
        } else {
            echo "Invalid file format. Please upload a valid image file.";
        }
    } else {
        // No new image file is uploaded, retain the existing image filename
        $sql = "UPDATE news SET title=?, type=?, name=?, date=?, track=?, start_date=?, end_date=?, detail=? WHERE news_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $title, $type, $name, $date, $track, $startDate, $endDate, $detail, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: announcement.php?msg=Announcement Updated Successfully!");
        } else {
            echo "Failed: " . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Talisay Senior High School LMS Admin</title>
    <link rel="stylesheet" type="text/css" href="assets/css/create_announcements.css">
    <link rel="shortcut icon" href="../../images/trace.svg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="shortcut icon" href="../../images/trace.svg" />
</head>

<body>

    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: green; color: white;">
        New Announcement
    </nav>

    <div class="wrapper">
        <div class="container">
            <div class="text-center mb-4">
                <img src="assets/image/trace.svg" style="height: 25vh; width: 40vh; margin-top: -20px;">
            </div>

            <section class="content">
                <div class="container d-flex justify-content-center">
                    <form action="" method="post" enctype="multipart/form-data" style="width: 50vw; min-width: 300px;">
                        <div class="row" style="flex: 1; padding-bottom: 8vh;">
                            <div class="col-mb-4">
                                <div class="alert alert-danger alert-dismissible fade show" id="validationAlert"
                                    role="alert" style="display: none;">
                                    Please fill in all required fields.
                                </div>
                            </div>

                            <div></div>
                            <div class="col mb-4">
                                <label class="form-label">Announcement Title</label>
                                <input type="text" class="form-control" name="title" value="<?php echo $title ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Type</label>
                                <div class="select-with-icon">
                                    <select name="type" id="type" class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="announcement" 
                                        <?php if ($type === 'announcement') 
                                            echo 'selected'; ?>>Announcement
                                        </option>
                                        <option value="news"
                                        <?php if ($type === 'news') 
                                            echo 'selected'; ?>>News
                                        </option>
                                    </select>
                                    <i class="bi bi-chevron-down select-icon"></i>
                                </div>
                            </div>
                            <div class="div"></div>

                            <div class="col mb-4">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="<?php echo $name ?>">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Division / Track</label>
                                <div class="select-with-icon">
                                    <select name="track" id="track" class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="all" <?php if ($track === 'all')
                                            echo 'selected'; ?>>Show to All
                                        </option>
                                        <option value="stem" <?php if ($track === 'stem')
                                            echo 'selected'; ?>>STEM
                                        </option>
                                        <option value="humss" <?php if ($track === 'humss')
                                            echo 'selected'; ?>>HUMSS
                                        </option>
                                        <option value="abm" <?php if ($track === 'abm')
                                            echo 'selected'; ?>>ABM</option>
                                        <option value="mechanics" <?php if ($track === 'mechanics')
                                            echo 'selected'; ?>>
                                            MECHANICS</option>
                                    </select>
                                    <i class="bi bi-chevron-down select-icon"></i>
                                </div>
                            </div>

                            <div></div>
                            <div class="col mb-4">
                                <label class="form-label">Start Date</label>
                                <div class="select-with-icon">
                                    <select name="start_date" id="start_date"
                                        class="form-control custom-select lightened-select">
                                        <option disabled value=""></option>
                                        <?php
                                        $startDateTimestamp = strtotime($startDate); // Convert the database date to a timestamp
                                        
                                        // Generate options for the next 30 days starting from the current date
                                        $currentDate = date('Y-m-d'); // Get the current date
                                        for ($i = 0; $i < 30; $i++) {
                                            $date = date('Y-m-d', strtotime("+$i days", strtotime($currentDate)));
                                            $selected = ($date === $startDate) ? 'selected' : ''; // Add the selected attribute if it matches the database value
                                            echo "<option value=\"$date\" $selected>$date</option>";
                                        }
                                        ?>
                                    </select>
                                    <i class="bi bi-chevron-down select-icon"></i>
                                </div>
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">End Date</label>
                                <div class="select-with-icon">
                                    <select name="end_date" id="end_date"
                                        class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <?php
                                        // Generate options for the next 30 days
                                        $startDate = strtotime('today');
                                        for ($i = 0; $i < 30; $i++) {
                                            $date = date('Y-m-d', strtotime("+$i days", $startDate));
                                            $selected = ($date === $endDate) ? 'selected' : '';
                                            echo "<option value=\"$date\" $selected>$date</option>";
                                        }
                                        ?>
                                    </select>
                                    <i class="bi bi-chevron-down select-icon"></i>
                                </div>
                            </div>

                            <div></div>
                            <div class="col mb-5">
                                <label class="form-label">Details</label>
                                <textarea class="details form-control" name="detail" id="exampleFormControlTextarea1"
                                    rows="12" style="padding: 10px;"><?php echo $detail ?></textarea>
                            </div>

                            <div></div>
                            <div class="col mb-5 d-flex align-items-center">
                                <label class="form-label" style="margin-right: 10px;">Attachment: </label>
                                <?php
                                // Retrieve the attachment file name from the database
                                $attachment = $row['attachment'];

                                if (!empty($attachment)) {
                                    // Check if the attachment file exists in the directory
                                    $attachmentPath = 'assets/image/announcement_upload/' . $attachment;
                                    if (file_exists($attachmentPath)) {
                                        // Check if the file is an image based on its extension
                                        $fileExtension = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
                                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

                                        if (in_array($fileExtension, $allowedExtensions)) {
                                            // Display the image
                                            echo '<img src="' . $attachmentPath . '" width="100" height="100" style="border-radius: 10px; margin-right: 10px;">';
                                        } else {
                                            // Display a link or icon for non-image attachments
                                            echo '<i class="bi bi-file-earmark"></i> ' . $attachment;
                                        }
                                    } else {
                                        echo 'Attachment not found.';
                                    }
                                } else {
                                    echo 'No attachment.';
                                }
                                ?>
                                <input class="form-control custom-input" type="file" name="attachment"
                                    id="formFileMultiple" accept="image/*" style="width: 36%;">
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-4">
                                <a href="announcement.php" class="btn btn-outline-danger"
                                    style="padding: 7px; font-size: 15px; margin-right: 2vh;">Cancel</a>
                                <button type="submit" class="btn btn-outline-success" name="submit"
                                    style="padding: 7px; font-size: 15px;">Update</button>
                            </div>

                        </div>
                    </form>
                </div>
            </section>
        </div>

        <div class="footer"></div>
    </div>

    <script>
        // Get the form element
        var form = document.querySelector('form');

        // Get the alert element
        var validationAlert = document.getElementById('validationAlert');

        form.addEventListener('submit', function (event) {
            // Get the input fields and text area
            var titleInput = document.querySelector('input[name="title"]');
            var typeDropdown = document.querySelector('select[name="type"]');
            var nameInput = document.querySelector('input[name="name"]');
            var divisionDropdown = document.querySelector('select[name="track"]');
            var startDateDropdown = document.querySelector('select[name="start_date"]');
            var endDateDropdown = document.querySelector('select[name="end_date"]');
            var detailTextArea = document.querySelector('textarea[name="detail"]');

            var isEmpty = false;

            if (titleInput.value.trim() === '') {
                isEmpty = true;
                titleInput.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                titleInput.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (typeDropdown.value.trim() === '') {
                isEmpty = true;
                typeDropdown.classList.add('is-invalid');
            } else {
                typeDropdown.classList.add('is-invalid');
            }

            if (nameInput.value.trim() === '') {
                isEmpty = true;
                nameInput.classList.add('is-invalid');
            } else {
                nameInput.classList.remove('is-invalid');
            }

            if (divisionDropdown.value === '') {
                isEmpty = true;
                divisionDropdown.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                divisionDropdown.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            if (startDateDropdown.value === '') {
                isEmpty = true;
                startDateDropdown.classList.add('is-invalid');
            } else {
                startDateDropdown.classList.remove('is-invalid');
            }

            if (endDateDropdown.value === '') {
                isEmpty = true;
                endDateDropdown.classList.add('is-invalid');
            } else {
                endDateDropdown.classList.remove('is-invalid');
            }

            if (detailTextArea.value.trim() === '') {
                isEmpty = true;
                detailTextArea.classList.add('is-invalid'); // Add a class to highlight the invalid input
            } else {
                detailTextArea.classList.remove('is-invalid'); // Remove the class if it's valid
            }

            // Check if any required fields are empty
            if (titleInput.value === '' || typeDropdown.value === '' ||
                divisionDropdown.value === '' ||
                startDateDropdown.value === '' ||endDateDropdown.value === '' ||
                divisionDropdown.value === '' || detailTextArea.value === '') {
                // Prevent form submission
                event.preventDefault();

                // Show the alert
                validationAlert.style.display = 'block';

                // Scroll to the top
                setTimeout(function () {
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    // Focus on the alert element
                    validationAlert.focus();
                }, 100); // Adjust the timeout value as needed
            }
        });
    </script>
    <script>
        // Get the start date dropdown element
        var startDateDropdown = document.getElementById('start_date');

        // Add event listener for when the start date is changed
        startDateDropdown.addEventListener('change', function () {
            var startDate = this.value;
            var endDateDropdown = document.getElementById('end_date');

            // Enable the end date dropdown
            endDateDropdown.disabled = false;

            // Clear existing options
            endDateDropdown.innerHTML = '';

            // Add placeholder option
            var placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.disabled = true;
            placeholderOption.selected = true;
            placeholderOption.textContent = '';
            endDateDropdown.appendChild(placeholderOption);

            // Populate options based on the selected start date
            if (startDate) {
                var endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + 1); // Start from the day after the selected start date

                // Generate options for the next 30 days starting from the selected start date
                for (var i = 0; i < 30; i++) {
                    var date = new Date(endDate);
                    date.setDate(date.getDate() + i);
                    var option = document.createElement('option');
                    option.value = formatDate(date);
                    option.textContent = formatDate(date);
                    endDateDropdown.appendChild(option);
                }
            }
        });

        // Helper function to format the date as 'YYYY-MM-DD'
        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return year + '-' + month + '-' + day;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
</body>

</html>