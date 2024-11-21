<?php
include 'db.php';
session_start();

// Verificação de login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('conexao.php');

// Função para converter número em texto do status
function getStatusText($statusNumber)
{
    $statusMap = [
        '1' => 'Aberto',
        '2' => 'Atendimento',
        '3' => 'Fechado',
        '4' => 'Encerrado',
        '5' => 'Espera'
    ];
    return $statusMap[$statusNumber] ?? 'desconhecido';
}

// Buscar nome do usuário logado
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT tipo_usuario, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$userName = $userData['name'];
$userType = $userData['tipo_usuario'];

// Modifique a parte da query após a verificação do tipo de usuário
if ($userType == 'atendente') {
    $ticketQuery = "
        SELECT 
            t.id, 
            t.subject, 
            t.assignee, 
            t.status, 
            t.created_at, 
            t.category, 
            t.sector, 
            t.urgency
        FROM tickets t
        WHERE t.status IN (1, 2, 5)
        ORDER BY 
            FIELD(t.urgency, 'urgente', 'alta', 'média', 'baixa'), 
            t.created_at ASC
    ";
    $stmt = $conn->prepare($ticketQuery);
    $stmt->execute();
} else {
    $ticketQuery = "
        SELECT 
            t.id, 
            t.subject, 
            t.assignee, 
            t.status, 
            t.created_at, 
            t.category, 
            t.sector, 
            t.urgency
        FROM tickets t
        WHERE t.created_by = ?
        AND t.status IN (1, 2, 5)
        ORDER BY 
            FIELD(t.urgency, 'urgente', 'alta', 'média', 'baixa'), 
            t.created_at ASC
    ";
    $stmt = $conn->prepare($ticketQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
}
$ticketResult = $stmt->get_result();
// Prepara e executa a query
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos os Chamados - Help Desk</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="header">
        <a href="create_ticket.php" class="open-ticket-btn">
            <i class="fas fa-plus"></i> Novo Chamado
        </a>
        <h1>TODOS OS CHAMADOS</h1>
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

    <div class="content">
        <?php if ($ticketResult->num_rows > 0): ?>
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Assunto</th>
                        <th>Responsável</th>
                        <th>Status</th>
                        <th>Data de Abertura</th>
                        <th>Categoria</th>
                        <th>Setor</th>
                        <th>Urgência</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($ticket = $ticketResult->fetch_assoc()): ?>
                        <tr id="ticket-<?php echo $ticket['id']; ?>">
                            <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                            <td class="assignee-cell"><?php echo htmlspecialchars($ticket['assignee'] ? $ticket['assignee'] : 'nenhum'); ?></td>
                            <td data-status="<?php echo getStatusText($ticket['status']); ?>">
                                <?php echo getStatusText($ticket['status']); ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($ticket['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($ticket['category']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['sector']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['urgency']); ?></td>
                            <td>
                                <button onclick="editar(<?php echo $ticket['id']; ?>)">Editar</button>
                                <?php if ($ticket['assignee'] == 'nenhum' || empty($ticket['assignee'])): ?>
                                    <button onclick="copiar(<?php echo $ticket['id']; ?>)">Assumir</button>
                                <?php endif; ?>
                                <button onclick="remover(<?php echo $ticket['id']; ?>)">Remover</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><strong>Vazio:</strong> Não há chamados abertos no momento.</p>
        <?php endif; ?>
    </div>

    <script>
        // Same JavaScript functions as menu.php
    </script>
</body>

</html>