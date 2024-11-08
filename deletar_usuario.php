<?php
include('conexao.php');

// Verifica se o ID foi passado via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query para deletar o usuário pelo ID
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário deletado com sucesso!'); window.location.href = 'configuracao_usuario.php';</script>";
    } else {
        echo "<script>alert('Erro ao deletar usuário.'); window.location.href = 'configuracao_usuario.php';</script>";
    }
} else {
    echo "<script>alert('ID inválido.'); window.location.href = 'configuracao_usuario.php';</script>";
}

$conn->close();
?>
