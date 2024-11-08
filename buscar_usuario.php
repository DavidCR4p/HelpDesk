<?php
// Inclua a conexão com o banco de dados
include('conexao.php');

// Buscar os dados do usuário
$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Retornar os dados do usuário em formato JSON
echo json_encode($usuario);
?>