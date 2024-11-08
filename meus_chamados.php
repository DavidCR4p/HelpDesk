<!DOCTYPE html>
<html lang="pt-br">

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
$query = "SELECT name FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userName = $user['name'];

// Consulta apenas os chamados do usuário logado
$ticketQuery = "
    SELECT id, subject, assignee, status, created_at, category, sector, urgency
    FROM tickets
    WHERE assignee = ?
    ORDER BY
        FIELD(urgency, 'urgente', 'alta', 'média', 'baixa'),
        created_at asc
";

$stmt = $conn->prepare($ticketQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$ticketResult = $stmt->get_result();
$stmt->close();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Chamados - Help Desk</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="header">
        <a href="menu.php" class="open-ticket-btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <h1>MEUS CHAMADOS</h1>
        <div class="user-info">
            <button class="user-button">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?>
            </button>
        </div>
    </div>

    <div class="sidebar">
        <ul class="menu-list">
            <li>
                <a href="menu.php">
                    <i class="fas fa-home"></i>
                    <span class="menu-text">Menu Principal</span>
                </a>
            </li>
            <li>
                <a href="configuracao_usuario.php">
                    <i class="fas fa-user-plus"></i>
                    <span class="menu-text">Adicionar Usuário</span>
                </a>

            </li>
            <li>
                <a href="#">
                    <i class="fas fa-cogs"></i>
                    <span class="menu-text">Configurações</span>
                </a>
            </li>
            <li>
                <a href="meus_chamados.php">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="menu-text">Meus Chamados</span>
                </a>
            </li>
            <li>
                <a href="todos_chamados.php">
                    <i class="fas fa-list-alt"></i>
                    <span class="menu-text">Chamados Ativos</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-history"></i>
                    <span class="menu-text">Histórico de Chamados</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="menu-text">Sair</span>
                </a>
            </li>

        </ul>
    </div>

    <div class="content">
        <?php if ($ticketResult->num_rows > 0): ?>
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Assunto</th>
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
                            <td data-status="<?php echo getStatusText($ticket['status']); ?>">
                                <?php echo getStatusText($ticket['status']); ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($ticket['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($ticket['category']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['sector']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['urgency']); ?></td>
                            <td>
                                <button onclick="editar(<?php echo $ticket['id']; ?>)">Editar</button>
                                <button onclick="visualizar(<?php echo $ticket['id']; ?>)">Visualizar</button>
                                <button onclick="remover(<?php echo $ticket['id']; ?>)">Remover</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><strong>Vazio:</strong> Você não possui chamados atribuídos no momento.</p>
        <?php endif; ?>
    </div>

    <script>
        function visualizar(ticketId) {
            window.location.href = 'view_ticket.php?id=' + ticketId;
        }

        function editar(ticketId) {
            window.location.href = 'edit_ticket.php?id=' + ticketId;
        }
        function remover(ticketId) {
            ticketIdToRemove = ticketId;
            const modal = document.getElementById('confirmModal');
            modal.style.display = 'block';
        }
    </script>
</body>

</html>