<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php"); // Se não estiver logado, redireciona para o login
    exit();
}

// Mensagem de erro ou sucesso
$senhaAlterada = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Validação simples das senhas
    if ($nova_senha === $confirmar_senha) {
        // Atualiza a senha no banco de dados (neste exemplo, apenas no código, idealmente seria no banco)
        $_SESSION['senha'] = $nova_senha; // Aqui simula a alteração, em um caso real, você atualizaria no banco
        $senhaAlterada = 'Senha alterada com sucesso!';
    } else {
        $senhaAlterada = 'As senhas não coincidem!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZG-HELP - Mudar Senha</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="change-password-container">
        <h2>Alterar Senha</h2>

        <?php if (!empty($senhaAlterada)) : ?>
            <p><?php echo $senhaAlterada; ?></p>
        <?php endif; ?>

        <form action="mudar_senha.php" method="POST">
            <label for="nova_senha">Nova Senha:</label>
            <input type="password" id="nova_senha" name="nova_senha" required>

            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>

            <button type="submit">Alterar Senha</button>
        </form>
    </div>

</body>
</html>
