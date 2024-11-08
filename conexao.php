<?php
$host = 'localhost';
$user = 'root';          // Nome do usuário MySQL
$pass = 'root';          // Senha MySQL
$dbname = 'helpdesk_db'; // Nome do banco de dados

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
