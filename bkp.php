<?php
include 'db.php'; // Verifique se o caminho para o arquivo de conexão está correto
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    // Verifique se os campos estão preenchidos
    if (!empty($subject) && !empty($description)) {
        // Inserir no banco de dados
        $stmt = $pdo->prepare("INSERT INTO tickets (subject, description) VALUES (?, ?)");
        $stmt->execute([$subject, $description]);

        // Redireciona após a criação do ticket
        header("Location: index.php"); // Redireciona para a página de listagem de tickets
        exit();
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Chamado</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>Criar Novo Chamado</h1>

    <!-- Formulário para criar um novo chamado -->
    <form action="create_ticket.php" method="post">
        <label for="subject">Assunto</label>
        <input type="text" name="subject" id="subject" required>

        <label for="description">Descrição</label>
        <textarea name="description" id="description" required></textarea>

        <button type="submit">Criar Ticket</button>
    </form>

</body>
</html>