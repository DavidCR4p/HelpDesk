<?php
include 'conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $ticketId = $_POST['id'];
    $userEmail = $_SESSION['email'];
    
    // Busca o nome do usuário
    $userQuery = "SELECT name FROM users WHERE email = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("s", $userEmail);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $userData = $userResult->fetch_assoc();
    $userName = $userData['name'];
    
    // Atualiza o ticket com o nome do usuário
    $query = "UPDATE tickets SET assignee = ?, status = '2' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $userName, $ticketId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar o ticket']);
    }
    
    $stmt->close();
    $userStmt->close();
}
$conn->close();
