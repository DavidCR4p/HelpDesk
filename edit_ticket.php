<!DOCTYPE html>
<html lang="pt-br">
<?php
include 'db.php';
include 'conexao.php';
session_start();

// Verify login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT tipo_usuario, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$userName = $userData['name'];
$userType = $userData['tipo_usuario'];

// No início do arquivo, após as verificações de sessão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketId = $_POST['id'];
    $subject = $_POST['subject'];
    $category = $_POST['category'];
    $sector = $_POST['sector'];
    $urgency = $_POST['urgency'];
    $status = $_POST['status'];
    $assignee = $_POST['assignee'];  // Email do novo responsável
    $description = $_POST['description'];

    // Atualiza o ticket com o novo responsável
    $updateQuery = "UPDATE tickets SET 
        subject = ?, 
        category = ?, 
        sector = ?, 
        urgency = ?, 
        status = ?, 
        assignee = ?,  
        description = ? 
        WHERE id = ?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param(
        "sssssssi",
        $subject,
        $category,
        $sector,
        $urgency,
        $status,
        $assignee,
        $description,
        $ticketId
    );

    if ($stmt->execute()) {
        header("Location: menu.php");
        exit();
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}
// Fetch ticket data for display
if (isset($_GET['id'])) {
    $ticketId = $_GET['id'];

    $query = "SELECT * FROM tickets WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();
    $ticket = $result->fetch_assoc();

    if (!$ticket) {
        header("Location: menu.php");
        exit();
    }
} else {
    header("Location: menu.php");
    exit();
}
?>

<!-- Your HTML form should include all these fields and use POST method -->
<form method="POST" action="edit_ticket.php">
    <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">
    <!-- Rest of your form fields -->
</form>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Chamado</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="header">
        <a href="menu.php" class="open-ticket-btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <h1>Editar Chamado</h1>
        <div class="user-info">
            <button class="user-button">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?>
            </button>
        </div>
    </div>

    <div class="sidebar">
        <ul class="menu-list">
            <?php if ($userType == 'atendente'): ?>
                <li><a href="configuracao_usuario.php"><i class="fas fa-users-cog"></i><span class="menu-text">Adicionar Usuário</span></a></li>
               <!-- <li><a href="#"><i class="fas fa-cogs"></i><span class="menu-text">Configurações</span></a></li> -->
                <li><a href="meus_chamados.php"><i class="fas fa-ticket-alt"></i><span class="menu-text">Meus Chamados</span></a></li>
            <?php endif; ?>
            <li><a href="todos_chamados.php"><i class="fas fa-list-alt"></i><span class="menu-text">Chamados Ativos</span></a></li>
            <li><a href="historico_chamados.php"><i class="fas fa-history"></i><span class="menu-text">Histórico de Chamados</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Sair</span></a></li>
        </ul>
    </div>

    <div class="ticket-form-container">
        <form method="POST" action="edit_ticket.php?id=<?php echo htmlspecialchars($ticket['id']); ?>">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($ticket['id']); ?>">

            <label for="subject">Assunto</label>
            <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($ticket['subject']); ?>" readonly>

            <label for="category">Categoria</label>
            <select name="category" id="category" required>
                <option value="sistema" <?php echo ($ticket['category'] == 'sistema') ? 'selected' : ''; ?>>Sistema</option>
                <option value="infra" <?php echo ($ticket['category'] == 'infra') ? 'selected' : ''; ?>>Infraestrutura</option>
            </select>

            <label for="sector">Setor Solicitante</label>
            <input type="text" name="sector" id="sector" value="<?php echo htmlspecialchars($ticket['sector']); ?>" required>

            <label for="urgency">Urgência</label>
            <select name="urgency" id="urgency" required>
                <option value="urgente" <?php echo ($ticket['urgency'] == 'urgente') ? 'selected' : ''; ?>>Urgente</option>
                <option value="alta" <?php echo ($ticket['urgency'] == 'alta') ? 'selected' : ''; ?>>Alta</option>
                <option value="media" <?php echo ($ticket['urgency'] == 'media') ? 'selected' : ''; ?>>Média</option>
                <option value="baixa" <?php echo ($ticket['urgency'] == 'baixa') ? 'selected' : ''; ?>>Baixa</option>
            </select>

            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="1" <?php echo ($ticket['status'] == '1') ? 'selected' : ''; ?>>Aberto</option>
                <option value="2" <?php echo ($ticket['status'] == '2') ? 'selected' : ''; ?>>Andamento</option>
                <option value="3" <?php echo ($ticket['status'] == '3') ? 'selected' : ''; ?>>Fechado</option>
                <option value="5" <?php echo ($ticket['status'] == '5') ? 'selected' : ''; ?>>Espera</option>
            </select>

            <label for="attendant">Responsável</label>
            <select name="assignee" id="assignee" required>
                <option value="">Selecione um responsável</option>
                <?php
                $query_attendants = "SELECT email, name FROM users WHERE tipo_usuario = 'atendente'";
                if ($stmt_attendants = $conn->prepare($query_attendants)) {
                    $stmt_attendants->execute();
                    $result_attendants = $stmt_attendants->get_result();
                    while ($attendant = $result_attendants->fetch_assoc()) {
                        $selected = ($ticket['assignee'] == $attendant['email']) ? 'selected' : '';
                        echo "<option value='" . $attendant['email'] . "' " . $selected . ">"
                            . htmlspecialchars($attendant['name']) . "</option>";
                    }
                    $stmt_attendants->close();
                }
                ?>
            </select>


            <label for="description">Descrição</label>
            <textarea name="description" id="description" readonly><?php echo htmlspecialchars($ticket['description']); ?></textarea>

            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</body>

</html>