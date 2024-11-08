<?php
include 'db.php';
session_start();

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Criptografa a senha
    $role = $_POST['role']; // Pode ser 'admin' ou 'user'

    // Verifica se o e-mail já está cadastrado
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $error = "Este e-mail já está cadastrado.";
    } else {
        // Insere o novo usuário no banco de dados
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);

        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        $success = "Usuário cadastrado com sucesso!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuários</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Cadastro de Usuário</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <label for="name">Nome</label>
        <input type="text" name="name" required>

        <label for="email">E-mail</label>
        <input type="email" name="email" required>

        <label for="password">Senha</label>
        <input type="password" name="password" required>

        <label for="role">Tipo de Usuário</label>
        <select name="role" required>
            <option value="user">Usuário</option>
            <option value="admin">Administrador</option>
        </select>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>