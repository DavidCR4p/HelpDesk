<?php
session_start();
include('conexao.php');

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
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - Help Desk</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/alterar_senha.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="header">
        <a></a>
        <h1>ALTERAR SENHA</h1>
        <div class="user-info">
            <button class="user-button">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?>
            </button>
        </div>
    </div>

    <div class="content">
        <div class="password-change-form">
            <form action="processar_alterar_senha.php" method="POST">
                <div class="form-group">
                    <label for="nova_senha"><i class="fas fa-key"></i> Nova Senha:</label>
                    <input type="password" id="nova_senha" name="nova_senha" required>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha"><i class="fas fa-lock"></i> Confirmar Nova Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Salvar Nova Senha
                </button>
            </form>
        </div>
    </div>

    <style>
        .password-change-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</body>

</html>