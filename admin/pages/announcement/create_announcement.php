<?php
session_start();
include_once("config.php");

if (!isset($_SESSION['id'])) {
    header("Location:../../admin_login.php");
    exit();
}
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $track = $_POST['track'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $detail = $_POST['detail'];
    $name = $_POST['name'];
    $date = date('Y-m-d');

    // Check if a file was uploaded
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
        $file = $_FILES['attachment'];

        // Validate that it's an image
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate a unique filename and save the image
            $uniqueFilename = uniqid('picture_') . '.' . $fileExtension;
            $uploadDirectory = 'assets/image/announcement_upload/';
            $uploadPath = $uploadDirectory . $uniqueFilename;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // File was successfully uploaded, proceed with database insertion
                $sql = "INSERT INTO news (title, type, name, date, track, start_date, end_date, detail, attachment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtinsert = $db->prepare($sql);
                $result = $stmtinsert->execute([$title, $type, $name, $date, $track, $startDate, $endDate, $detail, $uniqueFilename]);

                if ($result) {
                    header("Location: announcement.php?msg=Announcement created successfully!");
                } else {
                    echo "Failed: " . mysqli_error($conn);
                }
            } else {
                echo "Failed to move the uploaded file.";
            }
        } else {
            echo "Invalid file type. Please upload an image (jpg, jpeg, png, gif).";
        }
    } else {
        echo "No file was uploaded.";
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
                                <input type="text" class="form-control" name="title">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Type</label>
                                <div class="select-with-icon">
                                    <select name="type" id="type" class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="announcement">Announcement</option>
                                        <option value="news">News</option>
                                    </select>
                                    <i class="bi bi-chevron-down select-icon"></i>
                                </div>
                            </div>

                            <div class="div"></div>

                            <div class="col mb-4">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name">
                            </div>

                            <div class="col mb-4">
                                <label class="form-label">Division / Track</label>
                                <div class="select-with-icon">
                                    <select name="track" id="track" class="form-control custom-select lightened-select">
                                        <option disabled selected value=""></option>
                                        <option value="all">Show to All</option>
                                        <option value="stem">STEM</option>
                                        <option value="humss">HUMSS</option>
                                        <option value="abm">ABM</option>
                                        <option value="mechanics">MECHANICS</option>
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
                                        <option disabled selected value=""></option>
                                        <?php
                                        // Generate options for the next 30 days
                                        $startDate = strtotime('today');
                                        for ($i = 0; $i < 30; $i++) {
                                            $date = date('Y-m-d', strtotime("+$i days", $startDate));
                                            echo "<option value=\"$date\">$date</option>";
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
                                    </select>
                                    <i class="bi bi-chevron-down select-icon"></i>
                                </div>
                            </div>

                            <div></div>
                            <div class="col mb-5">
                                <label class="form-label">Details</label>
                                <textarea class="details form-control" name="detail" id="exampleFormControlTextarea1"
                                    rows="12" style="padding: 10px;"></textarea>
                            </div>

                            <div></div>
                            <div class="col mb-5 d-flex align-items-center">
                                <label class="form-label" style="margin-right: 10px;">Attachment (Image Only): </label>
                                <input class="form-control custom-input" type="file" name="attachment" id="imageUpload"
                                    accept="image/*" multiple style="width: 36%;">
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-4">
                                <a href="announcement.php" class="btn btn-outline-danger"
                                    style="padding: 7px; font-size: 15px; margin-right: 2vh;">Cancel</a>
                                <button type="submit" class="btn btn-outline-success" name="submit"
                                    style="padding: 7px; font-size: 15px;">Create</button>
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
                typeDropdown.classList.remove('is-invalid');
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
                startDateDropdown.value === '' || endDateDropdown === '' ||
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
        // Add an event listener to the file input
        document.getElementById("imageUpload").addEventListener("change", function () {
            var files = this.files;
            var validExtensions = ["image/jpeg", "image/jpg", "image/png", "image/gif"];

            for (var i = 0; i < files.length; i++) {
                if (validExtensions.indexOf(files[i].type) === -1) {
                    alert("Invalid file type. Please upload an image (jpg, jpeg, png, gif).");
                    this.value = ""; // Clear the input
                    return false;
                }
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