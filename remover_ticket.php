<?php
include 'conexao.php'; // Inclua o arquivo de conexão

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Falha na conexão ao banco de dados.']));
}

// Verifique se o ID do ticket foi enviado
if (isset($_GET['id'])) {
    $ticketId = intval($_GET['id']);
    
    // Prepara a consulta para remover o ticket
    $stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $ticketId);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Ticket removido com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao remover o ticket.']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID do ticket não fornecido.']);
}

$conn->close();
?>
