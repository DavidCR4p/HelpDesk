<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $requer_troca_senha = isset($_POST['requer_troca_senha']) ? 1 : 0;

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (name, email, password, tipo_usuario, requer_troca_senha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $email, $password, $tipo_usuario, $requer_troca_senha);
    
    if ($stmt->execute()) {
        header("Location: configuracao_usuario.php?success=1");
    } else {
        header("Location: configuracao_usuario.php?error=1");
    }
    exit();
} else {
    header("Location: configuracao_usuario.php");
    exit();
}

$conn->close();