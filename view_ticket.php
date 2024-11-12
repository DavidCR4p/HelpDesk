<!DOCTYPE html>
<html lang="pt-br">
<?php
include 'db.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('conexao.php');

// Buscar nome do usuário logado
$email = $_SESSION['email'];
$query = "SELECT name FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userName = $user['name'];

// Buscar detalhes do ticket
$ticketId = $_GET['id'];
$ticketQuery = "SELECT * FROM tickets WHERE id = ?";
$stmt = $conn->prepare($ticketQuery);
$stmt->bind_param("i", $ticketId);
$stmt->execute();
$ticketResult = $stmt->get_result();
$ticket = $ticketResult->fetch_assoc();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Chamado - Help Desk</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="header">
        <a href="historico_chamados.php" class="open-ticket-btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <h1>VISUALIZAR CHAMADO #<?php echo $ticketId; ?></h1>
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
                <a href="historico_chamados.php">
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
        <div class="ticket-details">
            <div class="form-group">
                <label>Assunto:</label>
                <input type="text" value="<?php echo htmlspecialchars($ticket['subject']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Descrição:</label>
                <textarea readonly><?php echo htmlspecialchars($ticket['description']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Categoria:</label>
                    <input type="text" value="<?php echo htmlspecialchars($ticket['category']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Setor:</label>
                    <input type="text" value="<?php echo htmlspecialchars($ticket['sector']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Urgência:</label>
                    <input type="text" value="<?php echo htmlspecialchars($ticket['urgency']); ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Status:</label>
                    <input type="text" value="<?php echo $ticket['status'] == 3 ? 'Fechado' : 'Encerrado'; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Responsável:</label>
                    <input type="text" value="<?php echo htmlspecialchars($ticket['assignee']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Data de Abertura:</label>
                    <input type="text" value="<?php echo date('d/m/Y H:i:s', strtotime($ticket['created_at'])); ?>" readonly>
                </div>
            </div>

            <?php if (!empty($ticket['solution'])): ?>
            <div class="form-group">
                <label>Solução:</label>
                <textarea readonly><?php echo htmlspecialchars($ticket['solution']); ?></textarea>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .ticket-details {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input[readonly], textarea[readonly] {
            background-color: #f5f5f5;
            cursor: default;
        }
    </style>
</body>
</html>
