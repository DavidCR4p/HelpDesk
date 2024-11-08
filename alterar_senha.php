<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($nova_senha === $confirmar_senha) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = ?, primeiro_login = FALSE WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $senha_hash, $email);
        
        if ($stmt->execute()) {
            $message = "Senha alterada com sucesso!";
            header("Refresh: 2; URL=configuracao_usuario.php");
        } else {
            $message = "Erro ao alterar a senha.";
        }
    } else {
        $message = "As senhas não coincidem.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Alterar Senha</h2>
        <p>Por favor, altere sua senha no primeiro login.</p>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="password" name="nova_senha" placeholder="Nova Senha" required>
            <input type="password" name="confirmar_senha" placeholder="Confirmar Nova Senha" required>
            <button type="submit">Alterar Senha</button>
        </form>
    </div>
</body>
</html>