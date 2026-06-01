<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';

if ($action === 'signup') {
    $data = json_decode(file_get_contents("php://input"));
    
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_BCRYPT);
    
    $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "User registered successfully"]);
    } else {
        echo json_encode(["message" => "Registration failed"]);
    }
}

if ($action === 'login') {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $data->email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($data->password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            echo json_encode([
                "message" => "Login successful",
                "role" => $row['role']
            ]);
        }
    }
}
?>