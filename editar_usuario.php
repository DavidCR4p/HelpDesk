<?php
// editar_usuario.php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $requer_troca_senha = isset($_POST['requer_troca_senha']) ? 1 : 0;

    // Verifica se a senha foi alterada
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET name = ?, email = ?, password = ?, tipo_usuario = ?, requer_troca_senha = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssii", $name, $email, $password, $tipo_usuario, $requer_troca_senha, $id);
    } else {
        // Se a senha não foi alterada, não a incluímos na atualização
        $query = "UPDATE users SET name = ?, email = ?, tipo_usuario = ?, requer_troca_senha = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssii", $name, $email, $tipo_usuario, $requer_troca_senha, $id);
    }

    if ($stmt->execute()) {
        header("Location: configuracao_usuario.php?success=2");
    } else {
        header("Location: configuracao_usuario.php?error=2");
    }
    exit();
} else {
    header("Location: configuracao_usuario.php");
    exit();
}

$conn->close();