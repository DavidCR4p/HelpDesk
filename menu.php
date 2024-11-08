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

// Atualizar a consulta para mostrar o nome do responsável
$where_conditions = [];
$params = [];
$types = "";

if (!empty($_GET['categoria'])) {
    $where_conditions[] = "category = ?";
    $params[] = $_GET['categoria'];
    $types .= "s";
}

if (!empty($_GET['responsavel'])) {
    $where_conditions[] = "assignee = ?";
    $params[] = $_GET['responsavel'];
    $types .= "s";
}

if (!empty($_GET['status'])) {
    $where_conditions[] = "status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

if (!empty($_GET['urgencia'])) {
    $where_conditions[] = "urgency = ?";
    $params[] = $_GET['urgencia'];
    $types .= "s";
}

if (!empty($_GET['data'])) {
    $where_conditions[] = "DATE(created_at) = ?";
    $params[] = $_GET['data'];
    $types .= "s";
}

// Modifique a query para:
$ticketQuery = "
    SELECT id, subject, assignee, status, created_at, category, sector, urgency
    FROM tickets
    WHERE 1=1
";

// Adiciona as condições de filtro se existirem
if (!empty($where_conditions)) {
    $ticketQuery .= " AND " . implode(" AND ", $where_conditions);
}

// Adiciona a condição para mostrar apenas tickets sem responsável
$ticketQuery .= " AND (assignee IS NULL OR assignee = '')";

// Adiciona a ordenação
$ticketQuery .= " ORDER BY FIELD(urgency, 'urgente', 'alta', 'média', 'baixa'), created_at ASC";

// Execute a query
if (!empty($params)) {
    $stmt = $conn->prepare($ticketQuery);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $ticketResult = $stmt->get_result();
} else {
    $ticketResult = $conn->query($ticketQuery);
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Help Desk</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="js/scripts.js" defer></script>
</head>

<body>
    <!-- Modal de Confirmação -->
    <div id="confirmModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:white; padding:20px; margin:15% auto; width:30%; text-align:center; border-radius:10px;">
            <h3>Você deseja remover este item?</h3>
            <button id="confirmBtn">Sim</button>
            <button onclick="closeModal()">Não</button>
        </div>
    </div>

    <div class="header">
        <a href="create_ticket.php" class="open-ticket-btn">
            <i class="fas fa-plus"></i> Novo Chamado
        </a>
        <h1>CHAMADOS</h1>
        <div class="user-info">
            <button class="user-button">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?>
            </button>
        </div>
    </div>

    <div class="sidebar">
        <ul class="menu-list">
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
                            <td data-status="<?php echo getStatusText($ticket['status']); ?>"><?php echo getStatusText($ticket['status']); ?></td>
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
                                <button onclick="visualizar(<?php echo $ticket['id']; ?>)">Visualizar</button>
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
        function visualizar(ticketId) {
            window.location.href = 'view_ticket.php?id=' + ticketId;
        }

        let ticketIdToRemove = null;

        function remover(ticketId) {
            ticketIdToRemove = ticketId;
            const modal = document.getElementById('confirmModal');
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        document.getElementById('confirmBtn').addEventListener('click', function() {
            if (ticketIdToRemove !== null) {
                fetch('remover_ticket.php?id=' + ticketIdToRemove, {
                        method: 'GET'
                    })
                    .then(response => response.json())
                    .then(data => {
                        closeModal();
                        if (data.status === 'success') {
                            alert('Ticket removido com sucesso!');
                            location.reload();
                        } else {
                            alert('Erro ao remover o ticket: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao remover o ticket:', error);
                        alert('Ocorreu um erro ao tentar remover o ticket.');
                    });
            } else {
                closeModal();
            }
        });
          // Função para atualizar o conteúdo da tabela
          function updateTable() {
              fetch('/get_unassigned_tickets.php')  // Add leading slash
                  .then(response => response.text())
                  .then(data => {
                      document.querySelector('.ticket-table tbody').innerHTML = data;
                  });
          }
          // Atualiza a tabela a cada 5 segundos
          setInterval(updateTable, 5000);

          // Atualiza imediatamente após assumir um ticket
          function copiar(ticketId) {
              const xhr = new XMLHttpRequest();
              xhr.open('POST', 'assumir_ticket.php', true);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
              xhr.onreadystatechange = function() {
                  if (xhr.readyState === 4 && xhr.status === 200) {
                      updateTable(); // Atualiza tabela imediatamente
                      alert('Ticket assumido com sucesso!');
                  }
              };
              xhr.send('id=' + ticketId + '&status=2');
          }
        function editar(ticketId) {
            window.location.href = 'edit_ticket.php?id=' + ticketId;
        }
    </script>
</body>

</html>
<div class="filter-bar">
    <form method="GET" action="" class="filter-form">
        <select name="categoria" id="categoria">
            <option value="">Categoria</option>
            <option value="sistema">Sistema</option>
            <option value="infra">Infraestrutura</option>
        </select>

        <select name="responsavel" id="responsavel">
            <option value="">Responsável</option>
            <?php
            $queryUsers = "SELECT DISTINCT assignee FROM tickets WHERE assignee IS NOT NULL";
            $resultUsers = $conn->query($queryUsers);
            while ($user = $resultUsers->fetch_assoc()) {
                echo "<option value='" . $user['assignee'] . "'>" . $user['assignee'] . "</option>";
            }
            ?>
        </select>

        <select name="status" id="status">
            <option value="">Status</option>
            <option value="1">Aberto</option>
            <option value="2">Atendimento</option>
            <option value="3">Fechado</option>
            <option value="4">Encerrado</option>
            <option value="5">Espera</option>
        </select>

        <select name="urgencia" id="urgencia">
            <option value="">Urgência</option>
            <option value="urgente">Urgente</option>
            <option value="alta">Alta</option>
            <option value="media">Média</option>
            <option value="baixa">Baixa</option>
        </select>

        <input type="date" name="data" id="data">

        <button type="submit" class="filter-button">
            <i class="fas fa-filter"></i> Filtrar
        </button>

        <button type="reset" class="clear-button" onclick="window.location.href='menu.php'">
            <i class="fas fa-times"></i> Limpar Filtros
        </button>
    </form>
</div>