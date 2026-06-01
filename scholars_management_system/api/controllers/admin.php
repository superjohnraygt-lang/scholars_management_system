<?php
session_start();
require_once '../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Access denied"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $query = "SELECT a.*, u.email FROM applications a 
              JOIN users u ON a.user_id = u.id 
              ORDER BY a.submitted_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($applications);
}

if ($action === 'update_status') {
    $data = json_decode(file_get_contents("php://input"));
    $query = "UPDATE applications SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":status", $data->status);
    $stmt->bindParam(":id", $data->id);
    $stmt->execute();
    echo json_encode(["message" => "Status updated"]);
}
?>