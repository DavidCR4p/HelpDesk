<?php
session_start();
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $email = $_SESSION['email'];

    if ($nova_senha === $confirmar_senha) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ?, primeiro_login = 0, requer_troca_senha = 0 WHERE email = ?");
        $stmt->bind_param("ss", $senha_hash, $email);
        
        if ($stmt->execute()) {
            header("Location: menu.php");
            exit();
        }
    }
}

header("Location: alterar_senha.php?erro=1");
exit();
