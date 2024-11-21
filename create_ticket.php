<!DOCTYPE html>
<html lang="pt-br">

<?php
session_start();
include ('db.php');
include('conexao.php');


// Verificação de login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
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


$errorMessage = '';

// Buscar nome do usuário logado
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT tipo_usuario, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$userName = $userData['name'];
$userType = $userData['tipo_usuario'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $_POST['subject'] ?? '';
    $category = $_POST['category'] ?? '';
    $sector = $_POST['sector'] ?? '';
    $urgency = $_POST['urgency'] ?? '';
    $description = $_POST['description'] ?? '';
    $email = $_SESSION['email']; // Get email from session

    if (!empty($subject) && !empty($category) && !empty($sector) && !empty($urgency) && !empty($description)) {
        $validUrgencies = ['baixa', 'media', 'alta', 'urgente'];
        if (in_array($urgency, $validUrgencies)) {
            $stmt = $pdo->prepare("INSERT INTO tickets (subject, category, sector, urgency, description, status, created_by) VALUES (?, ?, ?, ?, ?, '1', ?)");
            $stmt = $pdo->prepare("
              INSERT INTO tickets 
                (subject, category, sector, urgency, description, status, created_by, created_at) 
                VALUES 
                (?, ?, ?, ?, ?, '1', ?, NOW())
            ");
            // Execute com todos os parâmetros, incluindo o email
            $stmt->execute([
                $subject,
                $category,
                $sector,
                $urgency,
                $description,
                $email // Email do usuário logado
            ]);
            header("Location: menu.php");
            exit();
        }
    }
} ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Chamado</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script>
        function showAlert(message) {
            alert(message);
        }
    </script>
</head>

<body>
    <?php
    if (!empty($errorMessage)) {
        echo "<script>showAlert('$errorMessage');</script>";
    }
    ?>

    <div class="header">
        <a href="menu.php" class="open-ticket-btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <h1>Criar Novo Chamado</h1>
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
        <form action="create_ticket.php" method="post">
            <label for="subject">Assunto</label>
            <input type="text" name="subject" id="subject" required>

            <label for="category">Categoria</label>
            <select name="category" id="category" required>
                <option value="">Selecione</option>
                <option value="sistema">Sistema</option>
                <option value="infra">Infraestrutura</option>
            </select>

            <label for="sector">Setor Solicitante</label>
            <input type="text" name="sector" id="sector" required>

            <label for="urgency">Urgência</label>
            <select name="urgency" id="urgency" required>
                <option value="">Selecione</option>
                <option value="urgente">Urgente</option>
                <option value="alta">Alta</option>
                <option value="media">Média</option>
                <option value="baixa">Baixa</option>
            </select>

            <label for="description">Descrição</label>
            <textarea name="description" id="description" required></textarea>

            <button type="submit">Abrir Chamado</button>
        </form>
    </div>
</body>

</html>