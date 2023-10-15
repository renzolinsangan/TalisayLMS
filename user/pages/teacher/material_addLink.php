<?php
include("db_conn.php");

if(isset($_POST['class_id']))
{
    $class_id = $_POST['class_id'];
}

if (isset($_POST['link'])) {
    $link = $_POST['link'];
    $_SESSION['temp_link'] = $link;

    $sql = "INSERT INTO classwork_material_upload (link, used) VALUES (?, 0)";
    $stmtinsert = $conn->prepare($sql);
    
    if ($stmtinsert === false) {
        echo "Prepare failed: " . $conn->error;
    }
    
    $result = $stmtinsert->execute([$link]);
    
    if ($result === false) {
        echo "Execute failed: " . $stmtinsert->error;
    } else {
        $lastInsertId = $conn->insert_id;
        $_SESSION['temp_file_id'] = $lastInsertId;
        echo "Link added successfully!";
    }
}
?>
