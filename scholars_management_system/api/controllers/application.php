<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $school = $_POST['school'];
    $program = $_POST['program'];
    $year_level = $_POST['year_level'];
    $gwa = $_POST['gwa'];
    
    // File upload handling with security
    $upload_dir = '../uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    
    $registration_cert = $_FILES['registration_cert']['name'];
    $school_id_image = $_FILES['school_id']['name'];
    $grades_copy = $_FILES['grades']['name'];
    
    // Validate file types
    if (in_array($_FILES['registration_cert']['type'], $allowed_types)) {
        move_uploaded_file($_FILES['registration_cert']['tmp_name'], 
            $upload_dir . $registration_cert);
    }
    
    // Similar validation for other files...
    
    $query = "INSERT INTO applications 
              (user_id, full_name, address, school, program, year_level, gwa, 
               registration_cert, school_id_image, grades_copy) 
              VALUES (:user_id, :full_name, :address, :school, :program, 
                      :year_level, :gwa, :reg_cert, :school_id, :grades)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":full_name", $full_name);
    $stmt->bindParam(":address", $address);
    $stmt->bindParam(":school", $school);
    $stmt->bindParam(":program", $program);
    $stmt->bindParam(":year_level", $year_level);
    $stmt->bindParam(":gwa", $gwa);
    $stmt->bindParam(":reg_cert", $registration_cert);
    $stmt->bindParam(":school_id", $school_id_image);
    $stmt->bindParam(":grades", $grades_copy);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Application submitted successfully"]);
    }
}
?>