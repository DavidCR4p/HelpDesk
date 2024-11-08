<?php
// Inclua a conexão com o banco de dados
include('conexao.php');
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Buscar nome do usuário logado
$email = $_SESSION['email'];
$query = "SELECT id, name, primeiro_login FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userName = $user['name'];

// Verificar se é o primeiro login
if ($user['primeiro_login']) {
    header("Location: alterar_senha.php"); // Redireciona para a página de alteração de senha
    exit();
}

// Query para buscar todos os usuários
if (isset($_GET['filtro'])) {
    $filtro = $_GET['filtro'];
    $userQuery = "SELECT id, name, email, tipo_usuario, requer_troca_senha FROM users WHERE name LIKE '%$filtro%' OR email LIKE '%$filtro%'";
} else {
    $userQuery = "SELECT id, name, email, tipo_usuario, requer_troca_senha FROM users";
}
$userResult = $conn->query($userQuery);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração de Usuários</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="button-container">
            <a href="menu.php" class="open-ticket-btn">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="#" class="open-ticket-btn" onclick="openPopup()">
                <i class="fas fa-user-plus"></i> Novo Usuário
            </a>
        </div>
        <h1>Configuração de Usuários</h1>
        <div class="user-info">
            <button class="user-button">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?>
            </button>
        </div>
    </div>

    <!-- Pop-up de novo usuário -->
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-content">
            <h2>Novo Usuário</h2>
            <form action="adicionar_usuario.php" method="POST">
                <input type="text" name="name" placeholder="Nome" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Senha" required>
                <select name="tipo_usuario">
                    <option value="user">Usuário</option>
                    <option value="atendente">Atendente</option>
                </select>
                <div>
                    <label>
                        <input type="checkbox" name="requer_troca_senha" checked>
                        Requer troca de senha no primeiro login
                    </label>
                </div>
                <button type="submit">Salvar</button>
                <button type="button" class="close-btn" onclick="closePopup()">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Pop-up de edição de usuário -->
    <div class="popup-overlay" id="editPopupOverlay">
        <div class="popup-content">
            <h2>Editar Usuário</h2>
            <form action="editar_usuario.php" method="POST">
                <input type="hidden" name="id" id="editUserId">
                <input type="text" name="name" id="editUserName" placeholder="Nome" required>
                <input type="email" name="email" id="editUserEmail" placeholder="E-mail" required>
                <input type="password" name="password" id="editUserPassword" placeholder="Nova Senha (deixe em branco para manter a atual)">
                <select name="tipo_usuario" id="editUserTipoUsuario">
                    <option value="user">Usuário</option>
                    <option value="atendente">Atendente</option>
                </select>
                <div>
                    <label>
                        <input type="checkbox" name="requer_troca_senha" id="editRequerTrocaSenha">
                        Requer troca de senha no próximo login
                    </label>
                </div>
                <button type="submit">Salvar</button>
                <button type="button" class="close-btn" onclick="closeEditPopup()">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
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

    <!-- Adicione o seguinte código antes da tabela de usuários -->
    <form action="" method="get">
        <input type="text" name="filtro" placeholder="Filtrar por nome ou e-mail">
        <button type="submit">Filtrar</button>
    </form>

    <!-- Conteúdo principal -->
    <div class="content">
        <?php if ($userResult->num_rows > 0): ?>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Função</th>

                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $userResult->fetch_assoc()): ?>
                        <tr id="user-<?php echo $user['id']; ?>">
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ? $user['email'] : 'nenhum'); ?></td>
                            <td><?php echo htmlspecialchars($user['tipo_usuario']); ?></td>

                            <td>
                                <button onclick="openEditPopup(<?php echo $user['id']; ?>, '<?php echo $user['name']; ?>', '<?php echo $user['email']; ?>', '<?php echo $user['tipo_usuario']; ?>', <?php echo $user['requer_troca_senha']; ?>)">Editar</button>
                                <button onclick="removerUsuario(<?php echo $user['id']; ?>)">Deletar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><strong>Vazio:</strong> Nenhum usuário encontrado no momento.</p>
        <?php endif; ?>
    </div>

    <script>
        function openPopup() {
            document.getElementById("popupOverlay").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("popupOverlay").style.display = "none";
        }

        function openEditPopup(id, name, email, tipoUsuario, requerTrocaSenha) {
            document.getElementById("editPopupOverlay").style.display = "flex";
            document.getElementById("editUserId").value = id;
            document.getElementById("editUserName").value = name;
            document.getElementById("editUserEmail").value = email;
            document.getElementById("editUserTipoUsuario").value = tipoUsuario;
            document.getElementById("editRequerTrocaSenha").checked = requerTrocaSenha;
        }

        function closeEditPopup() {
            document.getElementById("editPopupOverlay").style.display = "none";
        }

        function removerUsuario(id) {
            if (confirm('Tem certeza que deseja deletar este usuário?')) {
                window.location.href = 'deletar_usuario.php?id=' + id;
            }
        }
    </script>
</body>

</html>

<?php $conn->close(); ?>